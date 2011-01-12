<?php
class ClassLoader {
  private $callback;
  private $mapping;

  public function run() {
    $this->mapping = require HF_CACHE_PATH.__CLASS__.'.cache.php';
    spl_autoload_register($this->callback);
  }

  public function stop() {
    spl_autoload_unregister($this->callback);
  }

  public function load($name) {
    if (!isset($this->mapping['class'][$name])) {
      throw new InternalServerErrorException("Class '{$name}' not found");
    }
    $folder = $this->mapping['folder'][$this->mapping['class'][$name]];
    require $this->mapping['root'].$folder.DIRECTORY_SEPARATOR.$name.'.php';
  }
}