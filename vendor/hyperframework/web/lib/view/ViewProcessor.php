<?php
class ViewProcessor {
  public function run($cache) {
    if (!isset($_ENV['media_type'])) {
      $_ENV['media_type'] = 'Screen';
    }
    if (!isset($cache[$_ENV['media_type']])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$_ENV['media_type']];
    $view->render();
  }
}