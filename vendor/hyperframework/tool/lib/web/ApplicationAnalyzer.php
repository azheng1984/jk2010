<?php
class ApplicationAnalyzer {
  private $analyzers;
  private $cache;

  public function __construct($analyzers, $cache) {
    $this->analyzers = $analyzers;
    $this->cache = $cache;
  }

  public function execute($fileName, $relativeFolder, $rootFolder) {
    foreach ($this->analyzers as $name => $analyzer) {
      $fullPath = $rootFolder;
      if ($relativeFolder !== null) {
        $fullPath .= DIRECTORY_SEPARATOR.$relativeFolder;
      }
      $fullPath .= DIRECTORY_SEPARATOR.$fileName;
      $cache = $analyzer->execute($fileName, $fullPath);
      if ($cache !== null) {
        $this->cache->append(DIRECTORY_SEPARATOR.$relativeFolder, $name, $cache);
        return;
      }
    }
  }
}