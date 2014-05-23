<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class AssetCacheUrl {
    public function get($path) {
        $prefix = Config::get('hyperframework.web.asset_cache_prefix');
        if ($prefix !== null) {
            $path = $prefix . $path;
        }
        if (Config::get('hyperframework.web.enable_asset_cache_version')) {
            $segments = explode('.', $path);
            $cacheVersion = AssetCacheVersion::get($path);
            if (count($segments) === 1) {
                $result .= '-' . $cacheVersion;
            } else {
                $lastSegment = array_pop($segments);
                array_push($segments, $cacheVersion);
                array_push($segments, $lastSegment);
                $result = implode('.', $segments);
            }
        }
        return AssetUrl::get($result);
    }
}
