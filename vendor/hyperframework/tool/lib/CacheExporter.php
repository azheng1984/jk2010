<?php
class CacheExporter {
  private $cacheFolder;

  public function export($result) {
    if ($result === null) {
      return;
    }
    list($name, $cache) = $result->export();
    file_put_contents(
      $this->getCachePath($name),
      '<?php'.PHP_EOL.'return '.var_export($cache, true).';'
    );
  }

  private function getCachePath($name) {
    if ($this->cacheFolder === null) {
        $this->cacheFolder = 'cache';
        $this->createCacheFolder();
    }
    return $this->cacheFolder.DIRECTORY_SEPARATOR.$name.'.cache.php';
  }

  private function createCacheFolder() {
    if (!file_exists($this->cacheFolder)) {
      mkdir($this->cacheFolder);
      chmod($this->cacheFolder, 0777);
    }
  }
}