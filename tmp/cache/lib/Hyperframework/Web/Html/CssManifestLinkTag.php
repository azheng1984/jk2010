<?php
namespace Hyperframework\Web\Html;

use Hyperframework\Config;
use Hyperframework\Web\AssetUrl;
use Hyperframework\Web\AssetManifest;

class CssManifestLinkTag {
    public static function render(
        $path, $media = null, $shouldConcatenateFiles = null
    ) {
        if ($shouldConcatenateFiles === null) {
            $shouldConcatenateFiles = Config::get(
                'hyperframework.asset.concatenate_manifest'
            );
        }
        if ($shouldConcatenateFiles !== false) {
            self::renderItem($path, $media);
            return;
        }
        foreach (AssetManifest::getInnerUrlPaths($path) as $path) {
            self::renderItem($path, $media);
        }
    }

    private static function renderItem($path, $media) {
        echo '<link rel="stylesheet"';
        if ($media !== null) {
            echo ' media="', $media, '"';
        }
        echo ' href="', AssetUrl::get($path), '"/>';
    }
}
