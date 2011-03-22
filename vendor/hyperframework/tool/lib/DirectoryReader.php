<?php
class DirectoryReader {
  private $callback;

  public function __construct($callback) {
    $this->callback = $callback;
  }

  public function read(
    $rootPath = null, $relativePath = null, $isRecursive = true
  ) {
    $fullPath = $this->getFullPath($rootPath, $relativePath);
    if (is_file($fullPath)) {
      $this->callback->execute(
        basename($fullPath), $this->getDirectoryPath($relativePath), $rootPath
      );
      return;
    }
    if (!$isRecursive) {
      return;
    }
    if (substr($fullPath, -2) === DIRECTORY_SEPARATOR.'.') {
      $relativePath = $this->getDirectoryPath($relativePath);
      $isRecursive = false;
    }
    foreach (scandir($fullPath) as $entry) {
      if ($entry === '..' || $entry === '.') {
        continue;
      }
      if ($relativePath !== null) {
        $entry = $relativePath.DIRECTORY_SEPARATOR.$entry;
      }
      $this->read($rootPath, $entry, $isRecursive);
    }
  }

  private function getFullPath($rootPath, $relativePath) {
    $fullPath = $rootPath;
    if ($rootPath === null) {
      $fullPath = getcwd();
    }
    if ($relativePath !== null) {
      $fullPath .= DIRECTORY_SEPARATOR.$relativePath;
    }
    return $fullPath;
  }

  private function getDirectoryPath($value) {
    if ($value === null) {
      return;
    }
    return dirname($value);
  }
}