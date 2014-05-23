<?php
namespace Hyperframework\Web;

class AssetManifest {
    public static function getInnerUrlPaths($urlPath) {
        $path = self::getFullPath($urlPath);
        if ($path === null) {
            throw new Exception;
        }
        $result = array();
        $paths = self::getInnerPaths($path);
        foreach ($paths as $item) {
            $item = self::removeBasePath($item);
            if ($item === null) {
                throw new Exception;
            }
            $result[] = $item;
        }
        return $result;
    }

    public static function process($content) {
        $paths = self::getInnerPaths($path);
        $result = null;
        foreach ($paths as $path) {
            $result .= AssetFilterChain::process($path);
        }
        return $result;
    }

    private static function getInnerPaths($path) {
        $items = explode("\n", file_get_contents($path));
        //todo 解析 manifest 生成 inner paths
    }

    private static function removeBasePath($path) {
    }

    private static function getFullPath($urlPath) {
        //search file
    }
}
