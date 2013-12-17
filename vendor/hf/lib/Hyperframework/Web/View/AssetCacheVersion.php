<?php
namespace Hyperframework\Web\View;

class AssetCacheVersion {
    private static $manifest;

    public static function get($path) {
        return 1;
    }

    private static function getManifest() {
        if ($manifest === null) {
            
        }
        return static::$manifest;
    }
}
