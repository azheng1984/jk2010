<?php
class Router
{
  public static function run()
  {
    $cache = require SITE_ROOT_DIR.'cache/router.cache.php';
    if (isset($cache[$_SERVER['REQUEST_URI']])) {
      return $cache[$_SERVER['REQUEST_URI']];
    }
  }
}
