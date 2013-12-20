<?php
namespace Hyperframework\Web\View;

class AssetPreloader {
    private static $hasCss;
    private static $hasJs;

    public function hasCss() {
        if (static::$hasCss === null) {
            static::$hasCss = Config::get(__CLASS__ . '\HasCss') !== false;
        }
        return static::$hasCss;
    }

    public function hasJs() {
        if (static::$hasJs === null) {
            static::$hasJs = Config::get(__CLASS__ . '\HasJs') !== false;
        }
        return static::$hasJs;
    }

    public function getCssUrls() {
        $path = Config::get(
            __CLASS__ . '\CssPath', array('default' => 'app.css')
        );
        if (Config::get(__CLASS__ . '\CssManifestEnabled') === true) {
            return CssManifest::getUrls($path);
        }
        return array(CssUrl::get($path));
    }

    public function renderJsLink() {
        $path = Config::get(
            __CLASS__ . '\JsPath', array('default' => 'app.js')
        );
        if (Config::get(__CLASS__ . '\JsManifestEnabled') === true) {
            JsLink::renderManifest($path);
            return;
        }
        JsLink::render($path);
    }
}
