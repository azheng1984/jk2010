<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class Runner {
    public static function run() {
        $urlPath = static::getUrlPath();
        if (Config::get('hyperframework.web.enable_asset_proxy') === true) {
            $assetPath = static::getAssetPath($urlPath);
            if ($assetPath !== false) {
                static::runAssetProxy($assetPath);
                return;
            }
        }
        static::runApplication(static::getApplicationPath($urlPath));
    }

    protected static function getUrlPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if ($result === '') {
            return '/';
        }
        if ($result[0] === '#') {
            throw new NotFoundException;
        }
        return $result;
    }

    protected static function getAssetPath($urlPath) {
        $segments = parse_url();
        $prefix = AssetCachePathPrefix::get();
        if ($prefix === '/') {
            return $urlPath;
        }
        $prefixLength = strlen($prefix);
        if ($prefix[$perfixLength - 1] !== '/') {
            $prefix .= '/';
            $prefixLength += 1;
        }
        if (strncmp($urlPath, $prefix, $prefixLength) !== 0) {
            return false;
        }
        return substr($urlPath, $prefixLength - 1);
    }

    protected static function getApplicationPath($urlPath) {
        if (substr($path, -1) === '/') {
            return substr($path, 0, -1);
        }
        $extensionPosition = strpos($path, '.');
        if ($extensionPosition === false) {
            return $urlPath;
        }
        $_SERVER['REQUEST_MEDIA_TYPE'] = substr(
            $urlPath, $extensionPosition + 1
        );
        return substr($urlPath, 0, $extensionPosition);
    }

    protected static function runAssetProxy($path) {
        AssetProxy::run($path);
    }

    protected static function runApplication($path) {
        Application::run($path);
    }
}
