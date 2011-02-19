<?php
class ViewProcessor {
  private $media;

  public function __construct($media = 'screen') {
    $this->media= $media;
  }

  public function run($cache) {
    if (in_array($this->media, $cache, true)) {
      return;
    }
    if (!isset($cache[$this->media])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$this->media];
    $view->render();
  }
}