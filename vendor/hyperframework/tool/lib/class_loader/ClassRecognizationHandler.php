<?php
class ClassRecognizationHandler {
  private $cache;

  public function __construct($cache) {
    $this->cache = $cache;
  }

  public function handle($fullPath, $relativeFolder, $rootFolder) {
    $recognizer = new ClassRecognizer;
    $class = $recognizer->getClass(basename($fullPath));
    if ($class !== null) {
      $this->cache->append($class, $fullPath, $relativeFolder, $rootFolder);
    }
  }
}