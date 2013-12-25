<?php
namespace Hyperframework\Web\Asset;

class CssPreloader {
    public static function enabled() {
        return Config::get(__CLASS__ . '\Enabled') !== false;
    }

    public static function render($path) {
        if (static::enabled() === false) {
            throw \Exception;
        }
        echo '<link type="text/css" rel="stylesheet" href="',
            CssUrl::get($path), '"';
        if (static::$media !== null) {
            echo ' media="', static::$media, '"';
        }
        echo '/>';
    }
}
