<?php
class ClassLoaderCache {
  private $cache;

  public function __construct($cache = array()) {
    $this->cache = $cache;
  }

  public function export() {
    return $this->cache;
  }

  public function add($name, $folder, $root) {
    
  }
}