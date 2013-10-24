<?php
class ExplorerContext {
  private static $writer;
  private static $cache = array();

  public static function getExplorer($type) {
    if (!isset(self::$cache[$type])) {
      $class = $type.'Explorer';
      self::$cache[$type] = new $class;
    }
    return self::$cache[$type];
  }

  public static function getWriter() {
    if (self::$writer === null) {
      self::$writer = new CommandWriter;
    }
    return self::$writer;
  }

  public static function reset() {
    self::$writer = null;
    self::$cache = array();
  }
}