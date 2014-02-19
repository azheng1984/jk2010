<?php
namespace Hyperframework\Web\Asset;

class JsPreloader {
    public static function enabled() {
        return Config::get(__CLASS__ . '\Enabled') !== false;
    }

    public static function render($path = 'app.js') {
        if (static::enabled() === false) {
            throw \Exception('Js preloader not enabled');
        }
        echo '<script type="text/javascript" src="',
            JsUrl::get($path), '"></script>';
    }
}
