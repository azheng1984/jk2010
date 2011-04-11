<?php
class ApplicationCache {
  private $cache;

  public function __construct($analyzers) {
    $processors = array();
    foreach ($analyzers as $key => $value) {
      $processors[$key] = $key.'Processor';
    }
    $this->cache = array($processors);
  }

  public function append($path, $name, $cache) {
    if (DIRECTORY_SEPARATOR !== '/') {
      $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }
    if (!isset($this->cache[$path])) {
      $this->cache[$path] = array();
    }
    $this->cache[$path][$name] = $cache;
  }

  public function export() {
    return array('application', $this->cache);
  }
}