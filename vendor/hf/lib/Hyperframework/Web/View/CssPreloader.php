<?php
namespace Hyperframework\Web\View;

class CssPreloader {
    public function getUrls($path = 'app.css') {
        if (Config::get(__CLASS__ . '\Enabled') !== false) {
            return null;
        }
        if (Config::get(__CLASS__ . '\MergeEnabled') === false) {
            return CssManifest::getUrls($path);
        }
        return array(CssUrl::get($path));
    }
}
