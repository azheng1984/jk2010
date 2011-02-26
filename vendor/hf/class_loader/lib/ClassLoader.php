<?php
class ClassLoader {
  private $callback;
  private $roots;
  private $folders;
  private $classes;

  public function run() {
    list($this->classes, $this->folders, $this->roots) = require(
      HF_CACHE_PATH.'class_loader'.DIRECTORY_SEPARATOR.__CLASS__.'.cache.php'
    );
    $this->callback = array($this, 'load');
    spl_autoload_register($this->callback);
  }

  public function stop() {
    spl_autoload_unregister($this->callback);
  }

  public function load($name) {
    if (!isset($this->classes[$name])) {
      throw new Exception("Class '$name' not found");
    }
    require(
      $this->getFolder($this->classes[$name]).DIRECTORY_SEPARATOR.$name.'.php'
    );
  }

  private function getFolder($index) {
    $folder = $this->folders[$index];
    if (is_array($folder)) {
      return $this->roots[$folder[0]].DIRECTORY_SEPARATOR.$folder[1];
    }
    return $this->roots[0].DIRECTORY_SEPARATOR.$folder;
  }
}