<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\EnvironmentBuilder;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::buildEnvironment($rootNamespace, $rootPath);
        if (static::isAsset()) {
            static::runAssetProxy();
            return;
        }
        static::runApp();
    }

    protected static function buildEnvironment($rootNamespace, $rootPath) {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::run($rootNamespace, $rootPath);
        ExceptionHandler::run();
    }

    protected static function isAsset() {
        if (Config::get('hyperframework.web.enable_asset_proxy') !== true) {
            return false;
        }
        $prefix = AssetCachePathPrefix::get();
        if (strncmp(RequestPath::get(), $prefix, strlen($prefix)) === 0) {
            return true;
        }
        return false;
    }

    protected static function runAssetProxy() {
        AssetProxy::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
