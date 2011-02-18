<?php
class ClassLoader {
  private $classes;
  private $folders;
  private $callback;

  public function run() {
    $cachePath = HF_CACHE_PATH.'class_loader'
                .DIRECTORY_SEPARATOR.__CLASS__.'.cache.php';
    list($this->classes, $this->folders) = require $cachePath;
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
    $path = $this->folders[$this->classes[$name]]
           .DIRECTORY_SEPARATOR.$name.'.php';
    require $path;
  }
}