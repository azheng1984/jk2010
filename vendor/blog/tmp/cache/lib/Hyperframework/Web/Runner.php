<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\EnvironmentBuilder;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        if (static::isAsset()) {
            static::runAssetProxy();
            return;
        }
        static::runApp();
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::run($rootNamespace, $rootPath);
        //ExceptionHandler::run();
    }

    protected static function isAsset() {
        if (Config::get('hyperframework.asset.enable_proxy') !== true) {
            return false;
        }
        $prefix = AssetPathPrefix::get() . '/';
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
