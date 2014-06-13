<?php
namespace Hyperframework\Web;

class Runner {
    public static function run() {
        $router = static::getRouter();
        $target = $router->execute();
        if ($target['is_asset']) {
            static::runAssetProxy($target['path']);
        }
        static::runApplication($target['path']);
    }

    protected static function getRouter() {
        return new Router;
    }

    protected static function runAssetProxy($path) {
        AssetProxy::run($path);
    }

    protected static function runApplication($path) {
        Application::run($path);
    }
}
