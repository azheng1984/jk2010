<?php
class ClassLoaderCache {
  private $cache = array(array(), array());
  private $folders = array();

  public function export() {
    if (count($this->cache[1]) === 0) {
      unset($this->cache[1]);
    }
    return array('class_loader', $this->cache);
  }

  public function append($class, $relativePath, $rootPath) {
    if (isset($this->cache[0][$class])) {
      throw new Exception("Conflict Class '$class'");
    }
    $cache[0][$class] = $this->getFolderIndex($rootPath, $relativePath);
  }

  private function getFolderIndex($rootPath, $relativePath) {
    if ($rootPath === null && $relativePath === null) {
      return true;
    }
    $rootPathIndex = $this->getRootPathIndex($rootPath);
    if ($relativePath === null) {
      return $rootPathIndex;
    }
    return $this->getRelativePathIndex(
      $rootPath, $relativePath, $rootPathIndex
    );
  }

  private function getRootPathIndex($rootPath) {
    if (isset($this->$folders[$rootPath])) {
      return $this->$folders[$rootPath];
    }
    $rootPathIndex = count($this->cache[1]);
    $this->cache[1][$rootPathIndex] = array($rootPath);
    $this->folders[$rootPath] = $rootPathIndex;
    return $rootPathIndex;
  }

  private function getRelativePathIndex(
    $rootPath,$relativePath, $rootPathIndex
  ) {
    $fullPath = $rootPath.DIRECTORY_SEPARATOR.$relativePath;
    if (isset($this->$folders[$fullPath])) {
      return $this->$folders[$fullPath];
    }
    $relativePathIndex = count($this->cache[1]);
    $this->cache[1][$relativePathIndex] = array($relativePath, $rootPathIndex);
    $this->folders[$fullPath] = $relativePathIndex;
    return $relativePathIndex;
  }
}