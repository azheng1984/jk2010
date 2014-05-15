<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class Runner {
    public static function run() {
        $urlPath = static::getUrlPath();
        $assetPath = static::getAssetPath($urlPath);
        if ($assetPath === false) {
            static::runApplication($urlPath);
            return;
        }
        static::runAssetProxy($assetPath);
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
        if (Config::get('hyperframework.web.enable_asset_proxy') !== true) {
            return false;
        }
        $segments = parse_url(AssetCacheUrlPrefix::get());
        if (isset($segments['path']) === false || $segments['path'] === '/') {
            return $urlPath;
        }
        $prefix = $segments['path'];
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

    protected static function runAssetProxy($urlPath) {
        AssetProxy::run($urlPath);
    }

    protected static function runApplication($urlPath) {
        $applicationPath = Router::run($urlPath);
        Application::run($applicationPath);
    }
}
