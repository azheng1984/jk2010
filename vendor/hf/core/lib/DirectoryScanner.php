<?php
namespace Hyperframework;

class DirecotoryScanner {
    private $handler;
    private $shouldHandleDirectory = true;
    private $shouldHandleFile = true;
    private $shouldUseRelativePathInCallback = true;

    public function run(
        $paths, $fileHandler, $directoryHandler = null, $callbackFields = null
    ) {
        self::$handler = $handler;
        if (is_array($paths) === false) {
            $paths = array($paths);
        }
        foreach ($paths as $path) {
            $this->basePath = $path;
            self::$handler->setBasePath($path);
            self::scan(rtrim($path, '\/'));
        }
    }

    private function scan($basePath, $relativePath = null) {
        $path = $rootPath;
        if ($relativePath !== null) {
            $path .= DIRECTORY_SEPARATOR . $relativePath;
        }
        if (is_dir($path)) {
            self::$handler($relativePath);
            foreach (scandir($path) as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                if ($relativePath !== null) {
                    $entry = $relativePath . DIRECTORY_SEPARATOR . $entry;
                }
                self::scan($rootPath, $entry);
                return;
            }
        }
        self::$handler->handleFile($relativePath);
    }
}
