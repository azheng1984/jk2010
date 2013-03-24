<?php
class Application {
  private static $cache;
  private $isViewDisabled;

  public function run($path = null, $cache = null) {
    if ($path === null) {
      $segmentList = explode('?', $_SERVER['REQUEST_URI'], 2);
      $path = $segmentList[0];
    }
    if ($cache !== null) {
      self::$cache = $cache;
    }
    if (self::$cache === null) {
      self::$cache = require CACHE_PATH.'application.cache.php';
    }
    if (!isset(self::$cache[$path])) {
      throw new NotFoundException("Application path '$path' not found");
    }
    if (isset(self::$cache[$path]['Action'])) {
      $processor = new ActionProcessor;
      $processor->run(self::$cache[$path]['Action']);
    }
    if (isset(self::$cache[$path]['View']) && $this->isViewDisabled !== true) {
      $processor = new ViewProcessor;
      $processor->run(self::$cache[$path]['View']);
    }
  }

  public function redirect($location, $status = '302 Found') {
    header('HTTP/1.1 '.$status);
    header('Location: '.$location);
    $this->isViewDisabled = true;
  }
}