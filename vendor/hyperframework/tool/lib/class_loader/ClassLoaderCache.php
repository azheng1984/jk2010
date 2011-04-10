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

  public function append($class, $relativeFolder, $rootFolder) {
    if (isset($this->cache[0][$class])) {
      print_r($this->cache);
      throw new Exception("Conflict Class '$class'(file:$rootFolder $relativeFolder)");
    }
    $this->cache[0][$class] = $this->getIndex($rootFolder, $relativeFolder);
  }

  private function getIndex($rootFolder, $relativeFolder) {
    if ($rootFolder === null && $relativeFolder === null) {
      return true;
    }
    if ($rootFolder === null) {
      return $this->getFolderIndex($relativeFolder, $relativeFolder);
    }
    $rootFolderIndex = $this->getFolderIndex($rootFolder, array($rootFolder));
    if ($relativeFolder === null) {
      return $rootFolderIndex;
    }
    $fullPath = $rootFolder.DIRECTORY_SEPARATOR.$relativeFolder;
    return $this->getFolderIndex(
      $fullPath, array($relativeFolder, $rootFolderIndex)
    );
  }

  private function getFolderIndex($path, $cache) {
    if (isset($this->folders[$path])) {
      return $this->folders[$path];
    }
    $index = count($this->cache[1]);
    $this->cache[1][$index] = $cache;
    $this->folders[$path] = $index;
    return $index;
  }
}