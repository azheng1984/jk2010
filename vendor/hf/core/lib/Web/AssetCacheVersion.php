<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;

class AssetCacheVersion {
    private static $manifest;
    private static $current;
    private static $prefix;

    public static function get($path) {
        if (isset($manifest[$path])) {
            return self::getPrefix() . $manifest[$path];
        }
        self::getCurrent();
    }

    private static function getPrefix() {
        if (self::$prefix === null) {
            self::$prefix = Config::get(
                'asset_cache.version_prefix',
                'asset_cache' . DIRECTORY_SEPARATOR . 'manifest.php'
            );
            if (self::$prefix === null) {
                self::$prefix = '';
            }
        }
        return self::$prefix;
    }

    private static function getCurrent() {
        if (self::$current === null) {
            self::$current = self::getPrefix() . ConfigFileLoader::loadPhp(
                'asset_cache.version_path',
                'asset_cache' . DIRECTORY_SEPARATOR . 'version.php'
            );
        }
        return self::$current;
    }

    private static function getManifest() {
        if (self::$manifest === null) {
            self::$manifest = PhpFileConfigLoader::load(
                'asset_cache.manifest_path',
                'asset_cache' . DIRECTORY_SEPARATOR . 'manifest.php'
            );
        }
        return self::$manifest;
    }
}
