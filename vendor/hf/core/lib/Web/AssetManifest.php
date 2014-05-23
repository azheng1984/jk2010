<?php
namespace Hyperframework\Web;

class AssetManifest {
    public static function getInnerUrlPaths($urlPath) {
        $path = self::getFullPath($urlPath);
        if ($path === null) {
            throw new Exception;
        }
        $result = array();
        $paths = self::parse($path);
        foreach ($paths as $item) {
            $item = self::removeBasePath($item);
            if ($item === null) {
                throw new Exception;
            }
            $result[] = $item;
        }
        return $result;
    }

    public static function getContent($path) {
        $paths = self::parse($path);
        $result = null;
        foreach ($paths as $path) {
            $result .= file_get_contents($path);
        }
        return $result;
    }

    private static function parse($path) {
        $items = explode("\n", file_get_contents($path));
        //todo
    }

    private static function removeBasePath($path) {
    }

    private static function getFullPath($urlPath) {
        //search file
    }
}
