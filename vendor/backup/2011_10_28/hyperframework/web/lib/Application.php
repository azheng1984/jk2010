<?php
class Application {
  private $cache;

  public function run($path = null, $cachePath = CACHE_PATH) {
    if ($path === null) {
      $sections = explode('?', $_SERVER['REQUEST_URI'], 2);
      $path = $sections[0];
    }
    if ($this->cache === null) {
      $this->cache = require $cachePath.'application.cache.php';
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