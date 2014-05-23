<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class AssetCacheUrl {
    public function get($path) {
        if (Config::get('hyperframework.web.enable_asset_cache_version')) {
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
        $prefix = Config::get('hyperframework.web.asset_cache_url_prefix');
        if ($prefix !== null) {
            $path = $prefix . $path;
        }
        return AssetUrl::get($path);
    }
}
