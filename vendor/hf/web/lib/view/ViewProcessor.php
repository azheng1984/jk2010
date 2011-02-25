<?php
class ViewProcessor {
  public function __construct($media = 'screen') {
    $_ENV['media'] = $media;
  }

  public function run($cache) {
    if (in_array($_ENV['media'], $cache, true)) {
      return;
    }
    if (!isset($cache[$_ENV['media']])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$_ENV['media']];
    $view->render();
  }
}