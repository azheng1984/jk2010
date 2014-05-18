<?php
namespace Hyperframework\Web;

use Hyperframework\DirectoryScanner;

class AssetCacheBuilder {
    public static function run() {
        DirectoryScanner::run(
            function() {

            }, self::getIncludePaths()
        );
    }

    private static function scanDirectory($rootPath, $relativePath) {
    }
}
