<?php

namespace Components;

use Illuminate\Support\Str;
use Illuminate\View\Compilers\ComponentTagCompiler;

class ComponentsCompiler extends ComponentTagCompiler
{
    /**
     * Compile the Blade component string for the given component and attributes.
     * @param string $component
     * @param array $attributes
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function componentString(string $component, array $attributes) {
        $data = collect($attributes);
        $data = $data->mapWithKeys(function ($value, $key) {
            return [Str::camel($key) => $value];
        });

        $fragment = new \rex_fragment();
        foreach ($data as $key => $value) {
            $fragment->setVar($key, $value, false);
        }
        return $fragment->parse('components/' . $component . '.php');
    }

    /**
     * Compile the opening tags within the given string.
     * @param string $value
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function compileOpeningTags(string $value) {
        $pattern = "/
            <
                \s*
                rex[-\:]([\w\-\:\.]*)
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                [\w\-:.@]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
                (?<![\/=\-])
            >
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];
            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);
            return $this->componentString($matches[1], $attributes);
        }, $value);
    }

    /**
     * Compile the closing tags within the given string.
     * @param string $value
     * @return string
     */
    protected function compileClosingTags(string $value) {
        return preg_replace("/<\/\s*rex[-\:][\w\-\:\.]*\s*>/", '', $value);
    }

    /**
     * Compile the self-closing tags within the given string.
     * @param string $value
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function compileSelfClosingTags(string $value) {
        $pattern = "/
            <
                \s*
                rex[-\:]([\w\-\:\.]*)
                \s*
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                [\w\-:.@]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
            \/>
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];
            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);
            return $this->componentString($matches[1], $attributes) . "\n";
        }, $value);
    }

    /**
     * Get an array of attributes from the given attribute string.
     * @param string $attributeString
     * @return array
     */
    protected function getAttributesFromAttributeString(string $attributeString) {
        $attributeString = $this->parseAttributeBag($attributeString);
        $attributeString = $this->parseBindAttributes($attributeString);
        $pattern = '/
            (?<attribute>[\w\-:.@]+)
            (
                =
                (?<value>
                    (
                        \"[^\"]+\"
                        |
                        \\\'[^\\\']+\\\'
                        |
                        [^\s>]+
                    )
                )
            )?
        /x';

        if (!preg_match_all($pattern, $attributeString, $matches, PREG_SET_ORDER)) {
            return [];
        }

        return collect($matches)->mapWithKeys(function ($match) {
            $attribute = $match['attribute'];
            $value = $match['value'] ?? null;

            if (is_null($value)) {
                $value = 'true';

                $attribute = Str::start($attribute, 'bind:');
            }

            $value = $this->stripQuotes($value);

            if (str_starts_with($attribute, 'bind:')) {
                $attribute = Str::after($attribute, 'bind:');

                $this->boundAttributes[$attribute] = true;
            } else {
                $value = $this->compileAttributeEchos($value);
            }

            if (str_starts_with($attribute, '::')) {
                $attribute = substr($attribute, 1);
            }

            return [$attribute => $value];
        })->toArray();
    }
}