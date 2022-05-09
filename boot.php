<?php
require 'lib/vendor/autoload.php';

/** modules */
rex_extension::register('GENERATE_FILTER', '\Components\Components::parseComponents');

/** templates/other */
rex_extension::register('OUTPUT_FILTER', '\Components\Components::parseComponents');
