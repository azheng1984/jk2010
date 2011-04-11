<?php
class ApplicationConfiguration {
  public function extract($config) {
    if (!is_array($config)) {
      $config = array($config);
    }
    $handlers = array();
    foreach ($config as $key => $value) {
      if (is_int($key)) {
        $class = $value.'Handler';
        $handlers[$value] = new $class;
        continue;
      }
      $class = $key.'Handler';
      //TODO: check config argument is matched
      $handlers[$key] = new $class($value);
    }
    return $handlers;
  }
}