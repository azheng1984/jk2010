<?php
class Application {
  private $processors;
  private static $isProcessed = false;
  private static $cache = array();

  public function __construct($processor/*, ...*/) {
    $this->processors = func_get_args();
  }

  public function run($path = null) {
    if ($path === null) {
      $path = $_SERVER['REQUEST_URI'];
    }
    foreach ($this->processors as $processor) {
      $this->process($processor, $path);
    }
    if (!self::$isProcessed) {
      throw new NotFoundException("Path '$path' not processed");
    }
  }

  private function process($processor, $path) {
    $class = get_class($processor);
    if (!isset(self::$cache[$class])) {
      self::$cache[$class] = require $processor->getCachePath();
    }
    if (isset(self::$cache[$class][$path])) {
      $processor->run(self::$cache[$class][$path]);
      self::$isProcessed = true;
    }
  }

  public static function reset() {
    self::$cache = array();
    self::$isProcessed = false;
  }
}