<?php
class Application {
  private $processors;
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
    }
  }

  private function getCache($type, $path) {
    if (!isset(self::$cache[$type])) {
      $cachePath = HF_CACHE_PATH.'web'.DIRECTORY_SEPARATOR.'Processor'
                  .DIRECTORY_SEPARATOR.$type.'.cache.php';
      self::$cache[$type] = require $cachePath;
    }
    $cache = self::$cache[$type];
    if (!isset($cache[$path])) {
      $this->triggerCacheError("Path '$path' not found in '$type' cache");
    }
    return $cache[$path];
  }

  private function triggerCacheError($message) {
    if ($this->isFirst()) {
      throw new NotFoundException($message);
    }
    throw new InternalServerErrorException($message);
  }

  private function isFirst() {
    return count(self::$cache) > 1;
  }

  public static function reset() {
    self::$cache = array();
  }
}