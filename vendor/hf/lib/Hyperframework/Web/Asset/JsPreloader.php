<?php
namespace Hyperframework\Web\Asset;

class JsPreloader {
    public function getUrls($path = 'app.js') {
        if (Config::get(__CLASS__ . '\Enabled') === false) {
            return null;
        }
        if (Config::get(__CLASS__ . '\MergeEnabled') === false) {
            return JsManifest::getUrls($path);
        }
        return array(JsUrl::get($path));
    }
}
