<?php
class ApplicationBuilder {
  private $config;
  private $cache;

  public function __construct($config) {
    $this->config = $config;
    $processors = array();
    foreach ($this->config as $item => $config) {
      if (is_int($item)) {
        $item = $config;
        $config = null;
      }
      $processors[] = $item.'Processor';
    }
    $this->cache = array($processors);
  }

  public function build() {
    $directoryScanner = new DirectoryScanner('app');
    foreach ($directoryScanner->getFiles() as $file) {
      $this->analyze(
        $file['file_name'], $file['relative_path'], $file['full_path']
      );
    }
    return array('application', $this->cache);
  }

  private function analyze($fullPath, $path, $fileName) {
    foreach ($this->config as $item => $config) {
      if (is_int($item)) {
        $item = $config;
        $config = null;
      }
      $class = $item.'Builder';
      $builder = new $class($config);
      $cache = $builder->build($fileName, $fullPath);
      if (count($cache) === 0) {
        continue;
      }
      if (!isset($this->cache[$path])) {
        $this->cache[$path] = array();
      }
      if (isset($this->cache[$path][$item])) {
        $this->cache[$path][$item] += $cache;
        return;
      }
      $this->cache[$path][$item] = $cache;
      return;
    }
  }
}