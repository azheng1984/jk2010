<?php
class ViewProcessor {
  private $type;

  public function __construct($type = 'screen') {
    $this->type= $type;
  }

  public function run($cache) {
    $type = $this->type;
    if (!isset($cache[$type])) {
      throw new UnsupportedMediaTypeException;
    }
    if (empty($cache[$type])) {
      return;
    }
    $view = new $cache[$type];
    $view->render();
  }
}