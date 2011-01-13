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
    if ($cache[$type] == null) {
      return;
    }
    $view = new $cache[$type];
    $view->render();
  }
}