<?php
class Application {
  private $cache;

  public function run($path = null) {
    if ($path === null) {
      $path = $_SERVER['REQUEST_URI'];
    }
    if ($this->cache === null) {
      $this->cache = require CACHE_PATH.'application.cache.php';
    }
    if (!isset($this->cache[$path])) {
      throw new NotFoundException("Path '$path' not found");
    }
    foreach ($this->cache[0] as $name) {
      $class = $name.'Processor';
      $this->process($path, $name, new $class);
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