<?php
class Application {
  private $processors;
  private $cache;

  public function __construct($processors) {
    $this->processors = $processors;
  }

  public function run($path = null) {
    if ($path === null) {
      $path = $_SERVER['REQUEST_URI'];
    }
    if ($this->cache === null) {
      $this->cache = require CACHE_PATH.'application.cache.php';
    }
    if (!isset($this->cache[$path]) && !in_array($path, $this->cache, true)) {
      throw new NotFoundException("Path '$path' not found");
    }
    foreach ($this->processors as $name => $processor) {
      $this->process($path, $name, $processor);
    }
  }

  private function process($path, $name, $processor) {
    $cache = null;
    if (isset($this->cache[$path][$name])) {
      $cache = $this->cache[$path][$name];
    }
    if ($cache !== null || in_array($name, $this->cache[$path], true)) {
      $processor->run($cache);
    }
  }
}