<?php
class View
{
  public static function run($app)
  { 
    $type = 'Screen';
    if (strpos('m.', $_SERVER['HTTP_HOST']) === 0) {
      $type = 'Handheld';
    }
    $class = $app.$type;
    $view = new $class;
    $view->render();
  }
}
