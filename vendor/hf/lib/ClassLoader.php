<?php
class ClassLoader {
  private $classes;
  private $folders;
  private $callback;

  public function run() {
    list($this->classes, $this->folders) = require HF_CACHE_PATH.__CLASS__.'.cache.php';
    $this->callback = array($this, 'load');
    spl_autoload_register($this->callback);
  }

  public function stop() {
    spl_autoload_unregister($this->callback);
  }

  public function load($name) {
    if (!isset($this->classes[$name])) {
      throw new InternalServerErrorException("Class '{$name}' not found");
    }
    require $this->folders[$this->classes[$name]].DIRECTORY_SEPARATOR.$name.'.php';
  }
}