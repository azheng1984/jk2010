<?php
namespace Hyperframework\Web\View;
use Hyperframework\Config;

class Asset {
    private static $cache;
    private static $config;

    private static function getCache() {
        if (self::$cache === null) {
            static::$cache = \Hyperframework\CacheLoader::load(
                __CLASS__ . '\CachePath', 'asset'
            );
        }
        return self::$cache;
    }

    private static function getConfig() {
        if (self::$config === null) {
            static::$config = \Hyperframework\ConfigLoader::load(
                __CLASS__ . '\ConfigPath', 'asset'
            );
        }
        return self::$config;
    }

    private static function getPath($path) {
        $cache = self::getCache();
        if (isset($cache[$path])) {
            return $cache[$path];
        }
        $url = Asset::getUrl('/js/common', 'js');
        $url = Asset::getUrl('/js/app', 'js');
    }

    public static function getUrl($path, $extension) {
        $extension = '.' . $extension;
        $config = self::getConfig();
        if (isset($config['path'])) {
        }
        $cache = self::getCache();
        if (Config::get(__CLASS__ . '\EnablePrecompilation' === false)) {
            return $path . $extension;
        }
        return $path . $cache[$path] . $extension;
    }
}

Asset::getManifest('app_js');
// => /js/a.js & /js/b.js dev
// => /js/app-123.js production

Asset::getUrl('common', 'js');
// => /js/common.js dev
// => /js/common-123.js production

Html::includeJs('app'); // %root%/config/asset/manifest/js/app.config.php
Html::includeCss('common'); // css/common.css
Html::includeImage('bk', 'png'); // image/bk.png

//Asset::getPath('/css/common', 'css');
//Asset::getPath('/js/common', 'js');
