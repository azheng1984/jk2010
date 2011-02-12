<?php
class ViewProcessor {
  private $type;

  public function __construct($type = 'screen') {
    $this->type= $type;
  }

  public function run($cache) {
    if (in_array($this->type, $cache, true)) {
      return;
    }
    if (!isset($cache[$this->type])) {
      throw new UnsupportedMediaTypeException;
    }
    $view = new $cache[$this->type];
    $view->render();
  }
}