<?php
namespace Hyperframework\Web;

use Hyperframework\FullPathRecognizer;

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

    public static function process($basePath, $content) {
        $paths = self::getInnerPaths($basePath, $content);
        $result = null;
        foreach ($paths as $path) {
            $result .= AssetFilterChain::run($path);
        }
        return $result;
    }

    private static function getInnerPaths($basePath, $content) {
        $result = array();
        $items = explode("\n", $content);
        foreach ($items as $item) {
            $item = trim($item);
            if ($item === '') {
                continue;
            }
            if (FullPathRecognizer::isFull($item) === false) {
                $item = $basePath . DIRECTORY_SEPARATOR . $item;
            }
            if (is_dir($item)) {
                $scanner = new \Hyperframework\DirectoryScanner(function($path) use (&$result) {
                    $result[]= $path;
                });
                $scanner->scan($item);
            }
            $result[] = $item;
        }
        return $result;
    }

    private static function removeBasePath($path) {
    }

    private static function getFullPath($urlPath) {
    }
}
