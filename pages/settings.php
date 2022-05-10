<?php

/** @var rex_addon $this */

if (rex_post('config-submit', 'bool')) {
    $this->setConfig(rex_post('config', [
        ['frontend_only', 'bool'],
    ]));

    echo rex_view::success($this->i18n('saved'));
}

$content = '<fieldset class="rex-components">';

/**
 * articles
 */
$n = [];
$n['label'] = '<label for="rex_components_frontend_only">' . $this->i18n('frontend_only') . '</label>';
$n['field'] = '<input type="checkbox" id="rex_components_frontend_only" name="config[frontend_only]" value="1" ' . ($this->getConfig('frontend_only') ? ' checked="checked"' : '') . ' />';
$formElements[] = $n;

/**
 * render form
 */
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/checkbox.php');

$formElements = [];

$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="config-submit" value="1" ' . rex::getAccesskey($this->i18n('save'), 'save') . '>' . $this->i18n('save') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('flush', true);
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('settings'));
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

echo '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        ' . $content . '
    </form>';
