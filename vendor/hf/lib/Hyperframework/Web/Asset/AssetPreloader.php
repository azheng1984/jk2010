<?php
namespace Hyperframework\Web\Asset;

abstract class AssetPreloader {
    abstract protected static function getManifestUrls($path);
    abstract protected static function getUrl($path);
    abstract protected static function renderLink($url);

    public static function enabled() {
        return Config::get(get_called_class() . '\Enabled') !== false;
    }

    public static function render($path) {
        if (static::enabled() === false) {
            throw \Exception;
        }
        if (Config::get(get_called_class() . '\ShouldSeparateFiles') === true) {
            foreach (static::getManifestUrls($path) as $url) {
               static::renderLink($url);
            }
            return;
        }
        static::renderLink(static::getUrl($path));
    }
}
