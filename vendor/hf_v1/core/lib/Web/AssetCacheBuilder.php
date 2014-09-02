<?php
namespace Hyperframework\Web;

use Hyperframework\DirectoryScanner;
use Hyperframework\Config;

class AssetCacheBuilder {
    public static function run() {
        $outputRootPath = self::getOutputRootPath();
        if (is_dir($outputRootPath) === false) {
            mkdir($outputRootPath);
        }
        $fileHandler = function($fullPath, $relativePath)use($outputRootPath) {
            $result = AssetFilterChain::run($fullPath);
            $path = $outputRootPath . '/' . $relativePath;
            $path = AssetFilterChain::removeInternalFileNameExtensions($path);
            file_put_contents($path, $result);
        };
        $directoryHandler = function($fullPath, $relativePath) use (
            $outputRootPath
        ) {
            $outputPath = $outputRootPath . DIRECTORY_SEPARATOR . $relativePath;
            if (is_dir($outputPath) === false) {
                mkdir($outputPath);
            }
        };
        $scanner = new DirectoryScanner($fileHandler, $directoryHandler);
        foreach (AssetProxy::getIncludePaths() as $path) {
            $scanner->scan($path);
        }
    }

    public static function getOutputRootPath() {
        $path = Config::get('hyperframework.asset.cache_path');
        if ($path === null) {
            $path = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'public'
                . str_replace('/', DIRECTORY_SEPARATOR, AssetPathPrefix::get());
        } elseif (FullPathRecognizer::isFull($path)) {
            $path = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR . $path;
        }
        $version = AssetCacheVersion::get(null);
        return $path . '-' . $version;
    }
}
