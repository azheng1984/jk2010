<?php
namespace Hyperframework\Web;

class AssetCacheVersion {
    private static $manifest;

    public static function get($path) {
        if ($path === '/asset/common.js') {
            return 2;
        }
        return 1;
    }

    private static function getManifest() {
        if ($manifest === null) {
            self::$manifest = \Hyperframework\PhpFileDataLoader::load(
                'hyperframework'
            );
        }
        return self::$manifest;
    }
}
