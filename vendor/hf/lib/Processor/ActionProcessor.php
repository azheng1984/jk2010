<?php
class ActionProcessor {
  public function run($cache) {
    if (!isset($cache['class'])) {
      return;
    }
    $method = $_SERVER['REQUEST_METHOD'];
    if (!in_array($method, $cache['method'], true)) {
      throw new MethodNotAllowedException($cache['method']);
    }
    $action = new $cache['class'];
    $action->{$method}();
  }
}