<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class Runner {
    public static function run() {
        if (static::isAsset()) {
            static::runAssetProxy();
        }
        static::runApp();
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
