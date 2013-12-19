<?php
namespace Hyperframework\Web\View;

class AssetPreloader {
    private static $hasCss;
    private static $hasJs;

    public function hasCss() {
        if (static::$hasCss === null) {
            static::$hasCss = Config::get(__CLASS__ . '\HasCss') === true;
        }
        return static::$hasCss;
    }

    public function hasJs() {
        if (static::$hasJs === null) {
            static::$hasJs = Config::get(__CLASS__ . '\HasJs') === true;
        }
        return static::$hasJs;
    }

    public function renderCssLink() {
        CssLink::render(Config::get(
            __CLASS__ . '\CssPath', array('default' => 'app.css')
        ));
    }

    public function renderJsLink() {
        JsLink::render(Config::get(
            __CLASS__ . '\JsPath', array('default' => 'app.js')
        ));
    }
}
