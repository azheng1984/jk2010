<?php
class ViewProcessor {
  public function run($cache) {
    $type = $_SERVER['REQUEST_MEDIA_TYPE'];
    if (!isset($cache[$type])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$type];
    $view->render();
  }
}