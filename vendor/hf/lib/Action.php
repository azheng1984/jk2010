<?php
class Action
{
  public static function run($app)
  {
    if (!$app) {
      #require_once HF_DIR.'Exception.php';
      throw new Exception;
    }

    if (($method = self::getMethod()) == null) {
      #require_once 'Exception.php';
      throw new Exception;
    }

    $class = $app.'Action';
    //security check
    $action = new $class;
    $action->{$method}();
  }

  private static function getMethod()
  {
    if (!isset($_SERVER['REQUEST_METHOD'])) {
      throw new Exception;
    }

    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        return 'get';
      case 'POST':
        return 'post';
    } 
  }
}
