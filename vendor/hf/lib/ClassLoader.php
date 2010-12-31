<?php
class ClassLoader
{
  private static $path = array();
  private static $plugins = array();
  private static $callback = array(__CLASS__, 'load');

  public static function load($name)
  {
    if (!isset(self::$path[$name])) {
      throw new Exception($name.' not found'); 
    }
    require SITE_DIR.self::$path[$name];
  }

  public static function import($plugin)
  {
    if (!isset(self::$plugins[$plugin])) {
      self::$path += require SITE_DIR."cache/class_path/{$plugin}.cache.php";
      self::$plugins[$plugin] = true;
    }
  }

  public static function run()
  {
    spl_autoload_register(self::$callback);
  }

  public static function stop()
  {
    spl_autoload_unregister(self::$callback);
    self::$plugins = array();
    self::$path = null;
  }
}
