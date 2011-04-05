<?php
class ViewProcessor {
  public function run($cache) {
    if (isset($_SERVER['REQUEST_MEDIA_TYPE']) === false) {
      $_SERVER['REQUEST_MEDIA_TYPE'] = key($cache);
    }
    $type = $_SERVER['REQUEST_MEDIA_TYPE'];
    if (isset($cache[$type]) === false) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$type];
    $view->render();
  }
}