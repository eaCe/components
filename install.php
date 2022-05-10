<?php
/** @var rex_addon $this */

if (!$this->hasConfig()) {
    $this->setConfig([
        'frontend_only' => false,
    ]);
}
