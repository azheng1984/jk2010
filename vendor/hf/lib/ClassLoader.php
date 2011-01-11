<?php
class ClassLoader {
  private $mapping;
  private $callback;

  public function run() {
    $this->mapping = require HF_CACHE_PATH.__CLASS__.'.cache.php';
    $this->callback = array($this, 'load');
    spl_autoload_register($this->callback);
  }

  public function stop() {
    spl_autoload_unregister($this->callback);
  }

  public function load($name) {
    if (!isset($this->mapping[$name])) {
      throw new InternalServerErrorException("Class '{$name}' not found");
    }
    require ROOT_PATH.$this->mapping[$name].'/'.$name.'.php';
  }
}