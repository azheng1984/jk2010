<?php
namespace Hyperframework;

class DirecotoryScanner {
    private $fileHandler;
    private $directoryHandler;

    public function __construct($fileHandler, $directoryHandler = null) {
        $this->fileHandler = $fileHandler;
        $this->directoryHandler = $directoryHandler;
    }

    public function scan($path) {
        $realPath = realpath($path);
        if ($realPath === false) {
            throw new Exception("Path '" . $path . "' not found");
        }
        $this->execute($realPath);
    }

    private function execute($fullPath, $relativePath = null) {
        if (is_file($fullPath)) {
            if ($this->fileHandler !== null) {
                $callback = $this->fileHandler;
                $callback($fullPath, $relativePath);
            }
            return;
        }
        if ($this->directoryHandler !== null && $relativePath !== null) {
            $callback = $this->directoryHandler;
            $callback($fullPath, $relativePath);
        }
        foreach (scandir($fullPath) as $child) {
            if ($child === '.' || $child === '..') {
                continue;
            }
            $childFullPath = $fullPath . DIRECTORY_SEPARATOR . $child;
            if ($relativePath === null) {
                $this->execute($childFullPath, $child);
                continue;
            }
            $this->execute(
                $childFullPath, $relativePath . DIRECTORY_SEPARATOR . $child
            );
        }
    }
}
