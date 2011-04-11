<?php
class ClassRecognizationHandler {
  private $cache;

  public function __construct($cache) {
    $this->cache = $cache;
  }

  public function handle($fullPath, $relativeFolder, $rootFolder) {
    $class = $this->getClass(basename($fullPath));
    if ($class !== null) {
      $this->cache->append($class, $fullPath, $relativeFolder, $rootFolder);
    }
  }

  private function getClass($fileName) {
    $pattern = '/^([A-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*).php$/';
    if (preg_match($pattern, $fileName)) {
      return preg_replace('/.php$/', '', $fileName);
    }
  }
}