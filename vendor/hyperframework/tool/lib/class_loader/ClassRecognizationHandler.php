<?php
class ClassRecognizationHandler {
  private $cache;

  public function __construct($cache) {
    $this->cache = $cache;
  }

  public function handle($fullPath, $relativeFolder, $rootFolder) {
    $classRecognizer = new ClassRecognizer;
    $class = $classRecognizer->getClass(basename($fullPath));
    if ($class !== null) {
      $this->cache->append($class, $fullPath, $relativeFolder, $rootFolder);
    }
  }
}