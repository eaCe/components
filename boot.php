<?php
/** @var rex_addon $this */
require 'lib/vendor/autoload.php';

/** modules */
rex_extension::register('GENERATE_FILTER', '\Components\Components::parseComponents');

/** templates/other */
if($this->getConfig('frontend_only')) {
    if(rex::isFrontend()) {
        rex_extension::register('OUTPUT_FILTER', '\Components\Components::parseComponents');
    }
}
else {
    rex_extension::register('OUTPUT_FILTER', '\Components\Components::parseComponents');
}
