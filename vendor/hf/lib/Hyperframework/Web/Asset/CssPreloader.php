<?php
namespace Hyperframework\Web\Asset;

class CssPreloader extends AssetPreloader {
    private static $media;

    public static function setMedia($value) {
        return static::$media = $value;
    }

    protected static function getManifestUrls($path) {
        CssManifest::getUrls($path);
    }

    protected static function getUrl($path) {
        return CssUrl::get($path);
    }

    protected static function renderLink($url) {
        echo '<link type="text/css" rel="stylesheet" href="' , $url , '"';
        if (static::$media !== null) {
            echo ' media="', static::$media, '"';
        }
        echo '/>';
    }
}
