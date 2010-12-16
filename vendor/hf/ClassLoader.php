<?php
class ClassLoader
{
  private static $cache;
  private static $directoies;
  private static $root;

  public static function initialize()
  {
    self::$cache = require CLASS_PATH_CACHE_FILE;
    self::$directories =& self::$cache['_dir'];
    self::$root =& self::$cache['_root'];
    spl_autoload_register(array('ClassLoader', 'load'));
  }

  public static function load($class)
  {
    require ROOT_DIR.self::$cache[$class];
  }
}