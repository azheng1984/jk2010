<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class CssManifestHtmlLinkTag {
    //main.css.manifest
    public static function render($path, $media = null) {
        if (Config::get('hyperframework.web.concatenate_assets') === true) {
            self::renderItem($path, $media);
            return;
        }
        $manifest = new AssetManifest($path);
        $manifest->getConcatenatedContent();
        $manifest->getCacheUrl();
        foreach ($manifest->getInnerCacheUrls() as $url) {
            self::renderItem($url, $media);
        }
    }

    private static function renderItem($url, $media) {
        echo '<link type="text/css" rel="stylesheet" href="',
            AssetCacheUrl::get($path), '"';
        if ($media !== null) {
            echo ' media="', $media, '"';
        }
        echo '/>';
    }
}
