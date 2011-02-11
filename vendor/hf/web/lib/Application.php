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

  private function getCache($type, $path) {
    if (!isset(self::$cache[$type])) {
      $cachePath = HF_CACHE_PATH.'web'.DIRECTORY_SEPARATOR.'Processor'
                  .DIRECTORY_SEPARATOR.$type.'.cache.php';
      self::$cache[$type] = require $cachePath;
    }
    if (!isset(self::$cache[$type][$path])) {
      $this->triggerCacheError("Path '$path' not found in '$type' cache");
    }
    return self::$cache[$type][$path];
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