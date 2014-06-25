<?php
namespace Hyperframework\Web;

class Runner {
    public static function run() {
        if (static::isAsset()) {
            static::runAssetProxy();
        }
        static::runApp();
    }

    protected function isAsset($urlPath) {
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
