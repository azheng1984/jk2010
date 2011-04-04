<?php
class ExplorerRenderingProxy {
  private $cache = array();

  public function render($type, $arguments) {
    if (!isset($this->cache[$type])) {
      $class = $type.'Explorer';
      $this->cache[$type] = new $class;
    }
    call_user_func_array(array($this->cache[$type], 'render'), $arguments);
  }
}