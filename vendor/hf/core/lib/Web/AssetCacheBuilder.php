<?php
namespace Hyperframework\Web;

use Hyperframework\DirectoryScanner;

class AssetCacheBuilder {
    public static function run() {
        $fileHandler = function($basePath, $relativePath) {
        }
        $directoryHandler = function($basePath, $relativePath) {
        }
        DirectoryScanner::run(
            self::getIncludePaths(), $fileHandler, $directoryHandler
        );
    }
}
