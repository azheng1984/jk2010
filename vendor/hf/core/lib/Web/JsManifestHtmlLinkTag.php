<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class JsManifestHtmlLinkTag {
    public static function render($path, $shouldConcatenateFiles = null) {
        if ($shouldConcatenateFiles === null) {
            $shouldConcatenateFiles =
                Config::get('hyperframework.web.concatenate_assets') === true;
        }
        if ($shouldConcatenateFiles === true) {
            self::renderItem($path);
            return;
        }
        foreach (AssetManifest::getPaths($path) as $path) {
            self::renderItem($path);
        }
    }

    private static function renderItem($path) {
        echo '<script src="', AssetCacheUrl::get($path), '"></script>';
    }
}
