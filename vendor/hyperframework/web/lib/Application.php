<?php
class Application {
  private $cache;

  public function run($path = null, $cache = null) {
    if ($path === null) {
      $segmentList = explode('?', $_SERVER['REQUEST_URI'], 2);
      $path = $segmentList[0];
    }
    if ($cache !== null) {
      $this->cache = $cache;
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