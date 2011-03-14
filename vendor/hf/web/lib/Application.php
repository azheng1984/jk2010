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
      $this->process($path, $name);
    }
  }

  private function process($path, $name) {
    $cache = null;
    if (isset($this->cache[$path][$name])) {
      $cache = $this->cache[$path][$name];
    }
    if ($cache !== null || in_array($name, $this->cache[$path], true)) {
      $class = $name.'Processor';
      $processor = new $class;
      $processor->run($cache);
    }
  }
}