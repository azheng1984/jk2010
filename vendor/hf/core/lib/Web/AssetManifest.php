<?php
namespace Hyperframework\Web;

class AssetManifest {
    public static function getInnerRelativePaths($relativePath) {
        $path = self::getFullPath($relativePath);
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

    private static function removeBasePath() {
    }

    private static function getFullPath($relativePath) {
    }
}
