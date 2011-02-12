<?php
class Application {
  private $processors;
  private static $isFirstProcessor = true;
  private static $cache = array();

  public function __construct($processor/*, ...*/) {
    $this->processors = func_get_args();
  }

  public function run($path = null) {
    if ($path == null) {
      $path = $_SERVER['REQUEST_URI'];
    }
    foreach ($this->processors as $processor) {
      $processor->run($this->getCache(get_class($processor), $path));
      self::$isFirstProcessor = false;
    }
  }

  private function getCache($class, $path) {
    if (!isset(self::$cache[$class])) {
      $cachePath = HF_CACHE_PATH.'web'.DIRECTORY_SEPARATOR.'Processor'
                  .DIRECTORY_SEPARATOR.$class.'.cache.php';
      self::$cache[$class] = require $cachePath;
    }
    if (!isset(self::$cache[$class][$path])) {
      $this->triggerCacheError("Path '$path' not found in '$class' cache");
    }
    return self::$cache[$class][$path];
  }

  private function triggerCacheError($message) {
    if (self::$isFirstProcessor) {
      throw new NotFoundException($message);
    }
    throw new InternalServerErrorException($message);
  }

  public static function reset() {
    self::$cache = array();
  }
}