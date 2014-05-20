<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class CssManifestHtmlLinkTag {
    public static function render($cachePath, $media = null) {
        if (Config::get('hyperframework.web.separate_asset_manifest_merging') === true) {
            echo '<link type="text/css" rel="stylesheet" href="',
                AssetCacheUrl::get($path), '"';
            if ($media !== null) {
                echo ' media="', $media, '"';
            }
            echo '/>';
        }
    }
}
