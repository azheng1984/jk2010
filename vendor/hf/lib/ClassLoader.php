<?php
class ClassLoader
{
  private static $cache;

  public static function load($class)
  {
    if (!isset(self::$cache[$class])) {
      throw new Exception($class." is not found"); 
    }
    require ROOT_DIR.self::$cache[$class];
  }

  public static function import($plugin) {
    if ($plugin == 'hf') {
      self::initialize();
      self::$cache = require ROOT_DIR.'cache/class_path.cache.php';
    }
  }

  private static function initialize()
  {
    spl_autoload_register(array('ClassLoader', 'load'));
  }
}
