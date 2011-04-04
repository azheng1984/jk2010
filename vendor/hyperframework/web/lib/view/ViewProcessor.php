<?php
class ViewProcessor {
  public function run($cache) {
    if (!isset($_SERVER['REQUEST_MEDIA_TYPE'])) {
      $_SERVER['REQUEST_MEDIA_TYPE'] = 'Screen';
    }
    if (!isset($cache[$_SERVER['REQUEST_MEDIA_TYPE']])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$_SERVER['REQUEST_MEDIA_TYPE']];
    $view->render();
  }
}