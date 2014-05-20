<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class CssManifestHtmlLinkTag {
    public static function render($path, $media = null) {
        if (Config::get('hyperframework.web.concatenate_assets') === true) {
            self::renderItem($path, $media);
            return;
        }
        $manifest = AssetManifest($path);
        foreach ($manifest->getPaths($path) as $path) {
            self::renderItem($path, $media);
        }
    }

    private static function renderItem($path, $media) {
        echo '<link type="text/css" rel="stylesheet" href="',
            AssetCacheUrl::get($path), '"';
        if ($media !== null) {
            echo ' media="', $media, '"';
        }
        echo '/>';
    }
}
