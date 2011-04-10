<?php
class ApplicationConfiguration {
  public function extract($config) {
    if (!is_array($config)) {
      $config = array($config);
    }
    $analyzers = array();
    foreach ($config as $key => $value) {
      if (is_int($key)) {
        $class = $value.'Analyzer';
        $analyzers[$value] = new $class;
        continue;
      }
      $class = $key.'Analyzer';
      //TODO: check config argument is matched
      $analyzers[$key] = new $class($value);
    }
    return $analyzers;
  }
}