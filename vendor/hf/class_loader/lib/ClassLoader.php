<?php
class ClassLoader {
  private $callback;
  private $classes;
  private $folders;

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
        $this->getFolder($this->classes[$name]).$name.'.php'
      );
    }
  }

  private function getFolder($index) {
    if ($index === true) {
      return ROOT_PATH;
    }
    $folder = $this->folders[$index];
    if (is_array($folder)) {
      return $this->getFullPath($folder).$folder[0].DIRECTORY_SEPARATOR;
    }
    return ROOT_PATH.$folder.DIRECTORY_SEPARATOR;
  }

  private function getFullPath($folder) {
    if (isset($folder[1])) {
      return $this->folders[$folder[1]][0].DIRECTORY_SEPARATOR;
    }
  }
}