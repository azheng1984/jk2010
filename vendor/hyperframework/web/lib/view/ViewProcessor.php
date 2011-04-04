<?php
class ViewProcessor {
  public function run($cache) {
    $type = isset($_SERVER['REQUEST_MEDIA_TYPE']) ?
      $_SERVER['REQUEST_MEDIA_TYPE'] : $cache[0];
    if (!isset($cache[$type])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$type];
    $view->render();
  }
}