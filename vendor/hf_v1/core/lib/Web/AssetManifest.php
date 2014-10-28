<?php
namespace Hyperframework\Web;

use Hyperframework;
use Hyperframework\FullPathRecognizer;

class AssetManifest {
    public static function getInnerUrlPaths($urlPath) {
        $basePath = dirname($urlPath);
        $urlPath = substr($urlPath, 1);
        $path = AssetProxy::searchFile($urlPath);
        if ($path === null) {
            throw new \Exception;
        }
        $result = array();
        $paths = self::getInnerPaths(dirname($path), file_get_contents($path));
        foreach ($paths as $item) {
            $item = self::getUrl($item);
            if ($item === null) {
                throw new \Exception;
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
                $scanner = new Hyperframework\DirectoryScanner(function($path) use (&$result) {
                    $result[]= $path;
                });
                $scanner->scan($item);
                continue;
            }
            $result[] = $item;
        }
        return $result;
    }

    private static function getUrl($path) {
        $includePaths = AssetProxy::getIncludePaths();
        foreach ($includePaths as $includePath) {
            if (FullPathRecognizer::isFull($includePath) === false) {
                $includePath = Hyperframework\APP_ROOT_PATH
                    . DIRECTORY_SEPARATOR . $includePath;
            }
            if (strncmp($includePath, $path, strlen($includePath)) === 0) {
                return AssetFilterChain::removeInternalFileNameExtensions(
                    substr($path, strlen($includePath)
                ));
            }
        }
    }
}
