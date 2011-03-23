<?php
class ApplicationAnalyzer {
  private $analyzers;
  private $cache;

  public function __construct($analyzers, $cache) {
    $this->analyzers = $analyzers;
    $this->cache = $cache;
  }

  public function execute($fileName, $relativePath, $rootPath) {
    foreach ($this->analyzers as $name => $analyzer) {
      $fullPath = $rootPath;
      if ($relativePath !== null) {
        $fullPath .= DIRECTORY_SEPARATOR.$relativePath;
      }
      $fullPath .= DIRECTORY_SEPARATOR.$fileName;
      $cache = $analyzer->execute($fileName, $fullPath);
      if ($cache !== null) {
        $this->cache->append(DIRECTORY_SEPARATOR.$relativePath, $name, $cache);
        return;
      }
    }
  }
}