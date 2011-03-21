<?php
class ApplicationBuilder {
  private $config;
  private $cache;

  public function __construct($config) {
    $this->config = $config;
    $processors = array();
    foreach ($this->config as $item) {
      $processors[$item] = $item.'Processor';
    }
    $this->cache = array($processors);
  }

  public function build() {
    $this->scan(null);
    return array('application', $this->cache);
  }

  private function scan($path) {
    $fullPath = $path;
    if (DIRECTORY_SEPARATOR !== '/') {
      $fullPath = str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
    $fullPath = (
      getcwd().DIRECTORY_SEPARATOR.'app'.$fullPath.DIRECTORY_SEPARATOR
    );
    $dirs = array();
    foreach (scandir($fullPath) as $entry) {
      if ($entry === '..' || $entry === '.') {
        continue;
      }
      if (is_dir($fullPath.$entry)) {
        $dirs[] = $entry;
        continue;
      }
      $this->dispatch(
        $fullPath.DIRECTORY_SEPARATOR.$entry, $path, $entry
      );
    }
    foreach ($dirs as $entry) {
      $this->scan($path.'/'.$entry);
    }
  }

  private function dispatch($fullPath, $path, $fileName) {
    foreach ($this->config as $item) {
      $class = $item.'Builder';
      $builder = new $class;
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