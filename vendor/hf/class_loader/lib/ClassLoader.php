<?php
class ClassLoader {
  private $callback;
  private $folders;
  private $classes;

  public function run() {
    list($this->classes, $this->folders) = require(
      CACHE_PATH.'class_loader.cache.php'
    );
    $this->callback = array($this, 'load');
    spl_autoload_register($this->callback);
  }

  public function stop() {
    spl_autoload_unregister($this->callback);
  }

  public function load($name) {
    if (isset($this->classes[$name])) {
      require(
        $this->getFolder($this->classes[$name]).DIRECTORY_SEPARATOR.$name.'.php'
      );
    }
  }

  private function getFolder($index) {
    $folder = $this->folders[$index];
    if (is_array($folder)) {
      return $this->getRoot($folder[1]).DIRECTORY_SEPARATOR.$folder[0];
    }
    return ROOT_PATH.$folder;
  }

  private function getRoot($index) {
    if ($index !== -1) {
      return $this->folders[$index][0];
    }
  }
}