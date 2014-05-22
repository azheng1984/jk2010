<?php
class AssetCacheUrl {
    public function get($path) {
        $result = Config::get('hyperframework.web.asset_cache_prefix') . '/' . $path;
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
        return $result;
    }
}
