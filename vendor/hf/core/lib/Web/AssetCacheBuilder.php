<?php
namespace Hyperframework\Web;

use Hyperframework\DirectoryScanner;

class AssetCacheBuilder {
    public static function run() {
        $outputRootPath = self::getOutputRootPath();
        $fileHandler = function($fullPath, $relativePath) {
            $result = AssetFilterChain::execute($fullPath);
            $relativePath = dirname($relativePath)
                . DIRECTORY_SEPARATOR . $result['file_name'];
            $isUpdated = self::compareContent(
                $result['content'], self::getCurrentCache($relativePath)
            );
            if ($isUpdated) {
                $version = self::updateVersion();
                self::outputCache($result['content'], $relativePath, $version);
            }
        }
        $directoryHandler = function($fullPath, $relativePath) use (
            $outputRootPath
        ) {
            $outputPath = $outputRootPath . DIRECTORY_SEPARATOR . $relativePath;
            if (is_dir($outputPath) === false) {
                mkdir($outputPath);
            }
        }
        $scanner = new DirectoryScanner($fileHandler, $directoryHanlder);
        foreach (self::getIncludePaths() as $path) {
            $scanner->scan($path);
        }
    }
}
