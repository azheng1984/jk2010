<?php
class ClassLoader
{
  private static $path;
  private static $plugins = array();
  private static $callback = array(__CLASS__, 'load');

  public static function load($class)
  {
    if (!isset(self::$path[$class])) {
      throw new Exception($class.' not found'); 
    }
    require SITE_DIR.self::$path[$class];
  }

  public static function import($plugin)
  {
    if (!isset(self::$plugins[$plugin])) {
      self::$path += require SITE_DIR."cache/class_path/{$plugin}.cache.php";
      self::$plugins[$plugin] = true;
    }
  }

  public static function run() {
    self::$path = require SITE_DIR."cache/class_path/cache.php";
    spl_autoload_register(self::$callback);
  }

  public static function stop() {
    spl_autoload_unregister(self::$callback);
    self::$plugins = array();
    self::$path = null;
  }
}
