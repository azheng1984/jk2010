<?php
class ApplicationCacheBuilder {
  private $config;

  public function __construct($config) {
    $this->config = $config;
  }

  public function build() {
    $cache = array($this->config);
    $this->buildApp('', $cache);
    $writer = new CacheWriter;
    $writer->write('application', $cache);
  }

  private function buildApp($path, &$cache) {
    $pathCache = array();
    $dirs = array();
    $dirPath = getcwd().'/app/'.$path;
    $processors = array();
    foreach ($this->config as $processor) {
      $class = $processor.'CacheBuilder';
      $processors[] = new $class;
    }
    foreach (scandir($dirPath) as $entry) {
      if ($entry === '..' || $entry === '.') {
        continue;
      }
      if (is_dir($dirPath.'/'.$entry)) {
        $dirs[] = $entry;
        continue;
      }
      foreach ($processors as $processor) {
        $processor->build($dirPath, $entry, $pathCache);
      }
    }
    if (count($pathCache) !== 0) {
      $cache[$path] = $pathCache;
    }
    foreach ($dirs as $entry) {
      $this->buildApp($path.'/'.$entry, $cache);
    }
  }
}