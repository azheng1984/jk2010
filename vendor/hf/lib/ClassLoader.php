<?php
class ClassLoader
{
  private static $cache = array();
  private static $plugins = array();

  public static function load($class)
  {
    if (!isset(self::$cache[$class])) {
      throw new Exception($class.' not found'); 
    }
    require SITE_ROOT_DIR.self::$cache[$class];
  }

  public static function import($plugin)
  {
    if (!isset(self::$plugins[$plugin])) {
      self::$cache += require SITE_ROOT_DIR."cache/class_path/{$plugin}.cache.php";
      self::$plugins[$plugin] = true;
    }
  }

  public static function run() {
    self::$cache += require SITE_ROOT_DIR."cache/class_path/cache.php";
    spl_autoload_register(array('ClassLoader', 'load'));
  }
}
