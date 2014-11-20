<?php
namespace Hyperframework\Web;

use Hyperframework\EnvironmentBuilder;
use Hyperframework\Config;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        if (self::isAssetProxyEnabled() && static::isAsset()) {
            static::runAssetProxy();
            return;
        }
        static::runApp();
    }

    protected static function initialize($rootNamespace, $rootPath) {
        chdir($rootPath);
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::build($rootNamespace, $rootPath);
        ErrorHandler::run();
    }

    final protected static function isAssetProxyEnabled() {
        return Config::get('hyperframework.asset.enable_proxy') === true;
    }

    protected static function isAsset() {
        $prefix = AssetPathPrefix::get() . '/';
        return strncmp(RequestPath::get(), $prefix, strlen($prefix)) === 0;
    }

    protected static function runAssetProxy() {
        AssetProxy::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
