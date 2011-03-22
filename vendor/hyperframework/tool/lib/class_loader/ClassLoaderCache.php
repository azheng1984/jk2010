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
    $cache[0][$class] = $this->getIndex($rootPath, $relativePath);
  }

  private function getIndex($rootPath, $relativePath) {
    if ($rootPath === null && $relativePath === null) {
      return true;
    }
    $rootPathIndex = $this->getFolderIndex(rootPath, array($rootPath));
    if ($relativePath === null) {
      return $rootPathIndex;
    }
    $fullPath = $rootPath.DIRECTORY_SEPARATOR.$relativePath;
    return $this->getFolderIndex(
      $fullPath, array($relativePath, $rootPathIndex)
    );
  }

  private function getFolderIndex($path, $cache) {
    if (isset($this->$folders[$path])) {
      return $this->$folders[$path];
    }
    $index = count($this->cache[1]);
    $this->cache[1][$index] = $cache;
    $this->folders[$path] = $index;
    return $index;
  }
}