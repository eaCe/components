<?php

namespace Components;

class Components
{
    /**
     * handle output
     * @param \rex_extension_point $ep
     * @return void
     * @throws \rex_exception
     */
    public static function parseComponents(\rex_extension_point $ep) {
        $c = new ComponentsCompiler();
        $t = $c->compile($ep->getSubject());
        $ep->setSubject($t);
    }
}