<?php
class ViewProcessor {
  public function __construct($mediaType = 'screen') {
    $_ENV['media_type'] = $mediaType;
  }

  public function run($cache) {
    if (!isset($cache[$_ENV['media_type']])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$_ENV['media_type']];
    $view->render();
  }
}