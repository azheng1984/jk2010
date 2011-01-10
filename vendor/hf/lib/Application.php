<?php
class Application {
  private $processors;
  private static $cache = array();
  private static $count = 0;

  public function __construct($processor/*, ...*/) {
    $this->processors = func_get_args();
  }

  public function run($path) {
    if ($path == null) {
      $path = $_SERVER['REQUEST_URI'];
    }
    foreach ($this->processors as $processor) {
      $processor->run($this->getCache(get_class($processor), $path));
      ++self::$count;
    }
  }

  private function getCache($type, $path) {
    if (!isset(self::$cache[$type])) {
      self::$cache[$type] = require SITE_PATH."cache/app/{$type}.cache.php";
    }
    $cache = self::$cache[$type];
    if (!isset($cache[$path])) {
      $this->error();
    }
    return $cache[$path];
  }

  private function error() {
    if (self::$count == 0) {
      throw new NotFoundException;
    }
    throw new InternalServerErrorException;
  }

  public static function reset()
  {
    self::$count = 0;
    self::$cache = null;
  }
}