<?php
namespace Hyperframework\Web;

use Hyperframework\DirectoryScanner;

class AssetCacheBuilder {
    public static function run() {
        $outputRootPath = self::getOutputRootPath();
        $fileHandler = function($fullPath, $relativePath) {
            $result = AssetFilterChain::execute($fullPath);
            $result['file_name'];
            $result['content'];
            self::updateVersion();
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
