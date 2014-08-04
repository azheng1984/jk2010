<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class AssetUrl {
    public function get($path) {
        if (Config::get(
            'hyperframework.asset.enable_versioning') !== false
        ) {
            $version = AssetCacheVersion::get($path);
            $segments = explode('.', $path);
            if (count($segments) === 1) {
                $path .= '-' . $version;
            } else {
                $lastSegment = array_pop($segments);
                array_push($segments, $version);
                array_push($segments, $lastSegment);
                $result = implode('.', $segments);
            }
        }
        return Config::get('hyperframework.asset.url_prefix')
            . AssetPathPrefix::get() . $path;
    }
}
