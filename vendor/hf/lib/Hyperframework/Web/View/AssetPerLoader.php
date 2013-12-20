<?php
namespace Hyperframework\Web\View;

class AssetPreloader {
    private static $hasCss;
    private static $hasJs;

    public function isCssPreloadEnabled() {
        if (static::$hasCss === null) {
            static::$isCssPreloadEnabled = Config::get(__CLASS__ . '\CssPreloadEnabled') !== false;
        }
        return static::$hasCss;
    }

    public function isJsEnabled() {
        if (static::$hasJs === null) {
            static::$is = Config::get(__CLASS__ . '\JsPreloadEnabled') !== false;
        }
        return static::$hasJs;
    }

    public function getCssUrls($path = 'app.css') {
        if (Config::get(__CLASS__ . '\CssManifestEnabled') === true) {
            return CssManifest::getUrls($path);
        }
        return array(CssUrl::get($path));
    }

    public function getJsUrls($path = 'app.js') {
        if (Config::get(__CLASS__ . '\JsManifestEnabled') === true) {
            return JsManifest::getUrls($path);
        }
        return array(JsUrl::get($path));
    }
}
