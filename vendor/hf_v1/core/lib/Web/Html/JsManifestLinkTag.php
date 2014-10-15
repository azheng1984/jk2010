<?php
namespace Hyperframework\Web\Html;

use Hyperframework\Config;

class JsManifestLinkTag {
    public static function render($path, $shouldConcatenateFiles = null) {
        if ($shouldConcatenateFiles === null) {
            $shouldConcatenateFiles = Config::get(
                'hyperframework.asset.manifest.concatenate_files'
            );
        }
        if ($shouldConcatenateFiles !== false) {
            self::renderItem($path);
            return;
        }
        foreach (AssetManifest::getInnerUrlPaths($path) as $path) {
            self::renderItem($path);
        }
    }

    private static function renderItem($path) {
        echo '<script src="', AssetCacheUrl::get($path), '"></script>';
    }
}
