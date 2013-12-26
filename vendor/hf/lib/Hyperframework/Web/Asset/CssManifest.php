<?php
namespace Hyperframework\Web\Asset;

class CssManifest {
    public static function render() {
        //fetch build url may be scan dir like class loader or asset proxy
        if ($shouldSeparateFiles) {
        //foreach () {
        //echo '@import url(/asset/css/)';
        //}
        return;
        }
        // foreach
        // require ...;
    }

    //todo 和 asset proxy 统一 asset 定位逻辑
    private static function getFullPath($path, $vendor) {
        $path = DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR
            . 'css' . DIRECTORY_SEPARATOR . $path;

        $suffix = DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR
            . 'css' . DIRECTORY_SEPARATOR . $path . '.manifest.php';
        $appPath = Config::get(
            'Hyperframework\AppPath', array('is_nullable' => false)
        );
        if ($vendor === null) {
            return $appPath . $suffix;
        }
        return $appPath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR
            . $vendor . DIRECTORY_SEPARATOR . $suffix;
    }

//    private static function getCacheFullPath($path, $vendor) {
//        $prefix = Config::get('Hyperframework\AppPath') . DIRECTORY_SEPARATOR
//            . 'data' . DIRECTORY_SEPARATOR . 'cache'
//            . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . 'manifest'
//            . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
//        if ($vendor !== null) {
//            $prefix .= 'vendor' . DIRECTORY_SEPARATOR . $vendor
//                . DIRECTORY_SEPARATOR;
//        }
//        return $prefix . $path . '.cache.php';
//    }
}
