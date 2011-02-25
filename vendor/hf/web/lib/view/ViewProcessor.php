<?php
class ViewProcessor {
  public function __construct($mediaType = 'screen') {
    $_ENV['media_type'] = $mediaType;
  }

  public function run($cache) {
    if (in_array($_ENV['media_type'], $cache, true)) {
      return;
    }
    if (!isset($cache[$_ENV['media_type']])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$_ENV['media_type']];
    $view->render();
  }
}