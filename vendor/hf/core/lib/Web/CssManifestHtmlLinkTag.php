<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class CssManifestLink {
    public static function render($path, $media = null) {
        Config::get('hyperframework.web.separate_asset_manifest_link');
        if (static::enabled() === false) {
            throw \Exception('Css preloader not enabled');
        }
        if (Config)
        echo '<link type="text/css" rel="stylesheet" href="',
            CssUrl::get($path), '"';
        if ($media !== null) {
            echo ' media="', $media, '"';
        }
        echo '/>';
    }
}
