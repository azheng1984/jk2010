<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class CssManifestHtmlLinkTag {
    public static function render(
        $path, $media = null, $shouldConcatenateFiles = null
    ) {
        if ($shouldConcatenateFiles === null) {
            $shouldConcatenateFiles = Config::get(
                'hyperframework.web.concatenate_asset_manifest_files'
            );
        }
        if ($shouldConcatenateFiles === false) {
            self::renderItem($path, $media);
            return;
        }
        foreach (AssetManifest::getPaths($path) as $path) {
            self::renderItem($path, $media);
        }
    }

    private static function renderItem($path, $media) {
        echo '<link rel="stylesheet"';
        if ($media !== null) {
            echo ' media="', $media, '"';
        }
        echo ' href="', AssetCacheUrl::get($path), '"/>';
    }
}
