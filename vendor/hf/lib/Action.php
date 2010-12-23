<?php
class Action
{
  public static function run($app)
  {
    if (!$app) {
      throw new Exception;
    }

    if (($method = self::getMethod()) == null) {
      throw new Exception;
    }

    $class = $app.'Action';
    //todo:is action exsited
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
