<?php
namespace Hyperframework\Web;

class DirecotoryScanner {
    private static $handler;

    public static function scan($handler, $paths) {
        self::$handler = $handler;
        if (is_array($paths) === false) {
            $paths = array($paths);
        }
        foreach ($paths as $path) {
            self::execute(rtrim($path, '\/'));
        }
    }

    private static function execute($rootPath, $relativePath = null) {
        $path = $rootPath;
        if ($relativePath !== null) {
            $path .= DIRECTORY_SEPARATOR . $relativePath;
        }
        if (is_file($path)) {
            self::$handler->handle($rootPath, $relativePath);
            return;
        }
        if (is_dir($path)) {
            foreach (scandir($path) as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                if ($relativePath !== null) {
                    $entry = $relativePath . DIRECTORY_SEPARATOR . $entry;
                }
                self::execute($rootPath, $entry);
            }
        }
        throw new Exception("Path '" . $path . "' not found");
    }
}
