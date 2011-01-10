<?php
class View {
  private $type;

  public function __construct($type = 'screen') {
    $this->type= $type;
  }

  public function run($cache) {
    $type = $this->type;
    if (!isset($cache[$type])) {
      throw new UnsupportedMediaTypeException;
    }
    if (!isset($cache[$type]['class'])) {
      return;
    }
    $view = new $cache[$type]['class'];
    $view->render();
  }
}