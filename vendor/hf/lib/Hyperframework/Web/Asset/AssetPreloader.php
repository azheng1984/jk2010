<?php
namespace Hyperframework\Web\Asset;

abstract class AssetPreloader {
    public static function enabled() {
        return Config::get(get_called_class() . '\Enabled') !== false;
    }

    public static function render($path) {
        if (static::enabled() === false) {
            throw \Exception;
        }
        $shouldRenderLinkManifest =
            Config::get(get_called_class() . '\ShouldRenderLinkManifest');
        if ($shouldRenderLinkManifest === true) {
            foreach (static::getManifestUrls($path) as $url) {
               static::renderLink($url);
            }
            return;
        }
        static::renderLink(static::getUrl($path));
    }

    abstract protected static function getManifestUrls($path) {}

    abstract protected static function getUrl($path) {}

    abstract protected static function renderLink($url) {}
}
