<?php
namespace Hyperframework\Web\Asset;

class CssPreloader extends AssetPreloader {
    public static function enabled() {
        return Config::get(get_called_class() . '\Enabled') !== false;
    }

    protected static function render($path = 'app.css') {
        if (static::enabled() === false) {
            throw \Exception;
        }
        if (Config::get(__CLASS__ . '\ShouldRenderManifest')) {
            static::renderUrls(CssManifest::getUrls($path));
        }
        static::renderUrls(array(CssUrl::get($path)));
    }

    protected static function renderUrls($urls) {
    }
}
