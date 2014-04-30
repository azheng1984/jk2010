<?php
namespace Hyperframework\Web;

class Runner {
    public static function run() {
        $urlPath = static::getUrlPath();
        if (static::isAsset($urlPath)) {
            static::runAssetProxy($urlPath);
            return;
        }
        static::runApplication($urlPath);
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

    protected static function isAsset($urlPath) {
        return strncmp($urlPath, '/assets/', 8) === 0;
    }

    protected static function runAssetProxy($urlPath) {
        AssetProxy::run($urlPath);
    }

    protected static function runApplication($urlPath) {
        $applicationPath = Router::run($urlPath);
        Application::run($applicationPath);
    }
}
