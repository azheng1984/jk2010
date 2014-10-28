<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class AssetUrl {
    public static function get($path) {
        if (Config::get(
            'hyperframework.asset.enable_versioning') !== false
        ) {
            $version = AssetCacheVersion::get($path);
            $pos = strrpos($path, '.');
            if ($pos === false) {
                $path .= '-' . $version;
            } else {
                $path = substr($path, 0, $pos)
                    . '.' . $version . '.' . substr($path, $pos + 1);
            }
        }
        return Config::get('hyperframework.asset.url_prefix')
            . AssetPathPrefix::get() . $path;
    }
}
