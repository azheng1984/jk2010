<?php
class DirectoryReader {
    private $handler;
    private $excludePaths;
    private $initPath;

    public function __construct($handler) {
        $this->handler = $handler;
    }

    public function read($rootPath, $relativePath = null, $excludePaths = array()) {
        $initPath = $this->getFullPath($rootPath, $relativePath);
        $this->excludePaths = $excludePaths;
        $this->execute(
            $this->format($rootPath), $this->format($relativePath), true
        );
    }

    private function execute($rootPath, $relativePath, $isRecursive) {
        $fullPath = $this->getFullPath($rootPath, $relativePath);
        if (in_array($fullPath, $this->excludePaths)) {
            return;
        } 
        if (!file_exists($fullPath)) {
            throw new Exception("Path '$fullPath' does not exist");
        }
        if (is_file($fullPath)) {
            $this->dispatch($fullPath, $relativePath, $rootPath);
            return;
        }
        if (!$isRecursive) {
            return;
        }
        if (substr($fullPath, -2) === DIRECTORY_SEPARATOR.'.') {
            list($rootPath, $relativePath) = $this->removeCurrentPath(
                $rootPath, $relativePath
            );
            $isRecursive = false;
        }
        foreach (scandir($fullPath) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if ($relativePath !== null) {
                $entry = $relativePath.DIRECTORY_SEPARATOR.$entry;
            }
            $this->execute($rootPath, $entry, $isRecursive);
        }
    }

    private function format($path) {
        if ($path === null) {
            return;
        }
        $search = DIRECTORY_SEPARATOR === '/' ? '\\' : '/';
        return str_replace($search, DIRECTORY_SEPARATOR, $path);
    }

    private function dispatch($fullPath, $relativePath, $rootPath) {
        if ($rootPath === $fullPath) {
            $rootPath = dirname($rootPath);
        }
        $this->handler->handle(
            $fullPath,
            $this->getDirectory($relativePath),
            $rootPath
        );
    }

    private function getFullPath($rootPath, $relativePath) {
        $fullPath = $rootPath;
        if ($rootPath === null) {
            $fullPath = $_SERVER['PWD'];
        }
        if ($relativePath !== null) {
            $fullPath .= DIRECTORY_SEPARATOR.$relativePath;
        }
        return $fullPath;
    }

    private function getDirectory($path) {
        if ($path === null) {
            return;
        }
        $result = dirname($path);
        if ($result === '.') {
            return;
        }
        return $result;
    }

    private function removeCurrentPath($rootPath, $relativePath) {
        if ($relativePath === null) {
            return array(dirname($rootPath), null);
        }
        if ($relativePath === '.') {
            return array($rootPath, null);
        }
        return array($rootPath, dirname($relativePath));
    }
}
