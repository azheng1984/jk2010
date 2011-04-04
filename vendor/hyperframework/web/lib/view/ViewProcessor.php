<?php
class ViewProcessor {
  public function run($cache) {
    $type = $this->getType();
    if (!isset($cache[$type])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$type];
    $view->render();
  }

  private function getType() {
    if (isset($_SERVER['REQUEST_MEDIA_TYPE'])) {
      return $_SERVER['REQUEST_MEDIA_TYPE'];
    }
    return 'Screen';
  }
}