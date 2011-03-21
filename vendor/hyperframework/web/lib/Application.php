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
    foreach ($this->cache[0] as $name => $class) {
      $this->process($path, $name, $class);
    }
  }

  private function process($path, $name, $class) {
    if (isset($this->cache[$path][$name])) {
      $processor = new $class;
      $processor->run($this->cache[$path][$name]);
    }
  }
}