<?php
namespace Hyperframework\Web;

class DirecotoryScanner {
    private $handler;
    private $excludePaths;

    public function __construct($handler) {
        $this->handler = $handler;
    }

    public function scan($includePaths, $excludePaths = null) {
        if (is_array($includePaths) === false) {
            $includePaths = array($includePaths);
        }
        $this->initializeExcludePaths($excludePaths);
        foreach ($includePaths as $includePath) {
            $this->execute(rtrim($includePath, '\/'));
        }
    }

    private function initializeExcludePaths($paths) {
        if ($paths === null) {
            return;
        }
        if (is_array($paths) === false) {
            $paths = array($paths);
        }
        $this->excludePath = array();
        foreach ($paths as $path) {
            $path = realpath($path);
            if ($path === false) {
                throw new Exception("Path '" . $path . "' not found");
            }
            $this->exceludePath[] = $path;
        }
    }

    private static function execute($includePath, $relativePath = null) {
        $path = $includePath;
        if ($relativePath !== null) {
            $path .= DIRECTORY_SEPARATOR . $relativePath;
        }
        if (is_file($path)) {
            self::$handler->handle($includePath, $relativePath);
            return;
        }
        if (is_dir($path)) {
            foreach (scandir($path) as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                if ($this->$excludePath !== null) {
                    $realPath = realpath($path . DIRECTORY_SEPARATOR . $entry);
                    if (in_array($realPath, $this->excludePaths)) {
                        continue;
                    }
                }
                if ($relativePath !== null) {
                    $entry = $relativePath . DIRECTORY_SEPARATOR . $entry;
                }
                $this->execute($includePath, $entry);
            }
        }
        throw new Exception("Path '" . $path . "' not found");
    }
}
