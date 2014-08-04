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
        return self::getDefault();
    }

    private static function getPrefix() {
        if (self::$prefix === null) {
            self::$prefix = Config::get('hyperframework.asset.version_prefix');
            if (self::$prefix === null) {
                self::$prefix = '';
            }
        }
        return self::$prefix;
    }

    private static function getDefault() {
        if (self::$current === null) {
            self::$current = self::getPrefix() . ConfigFileLoader::loadPhp(
                'asset' . DIRECTORY_SEPARATOR . 'version.php',
                'hyperframework.asset.version.config_path'
            );
        }
        return self::$current;
    }

    private static function getManifest() {
        if (self::$manifest === null) {
            self::$manifest = PhpFileConfigLoader::load(
                'asset' . DIRECTORY_SEPARATOR . 'version_manifest.php',
                'hyperframework.asset.version_manifest.config_path'
            );
        }
        return self::$manifest;
    }
}
