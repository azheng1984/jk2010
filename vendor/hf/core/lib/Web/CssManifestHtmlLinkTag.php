<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class CssManifestHtmlLinkTag {
    public static function render($path, $media = null) {
        if (Config::get('hyperframework.web.concatenate_assets') === true) {
            echo '<link type="text/css" rel="stylesheet" href="',
                AssetCacheUrl::get($path), '"';
            if ($media !== null) {
                echo ' media="', $media, '"';
            }
            echo '/>';
        }
    }
}
