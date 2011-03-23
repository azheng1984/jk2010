<?php
class ApplicationConfiguration {
  public function extract($config) {
    $analyzers = array();
    foreach ($config as $key => $value) {
      if (is_int($key)) {
        $class = $value.'Analyzer';
        $analyzers[$value] = new $class;
        continue;
      }
      $class = $key.'Analyzer';
      $analyzers[$key] = new $class($value);
    }
    return $analyzers;
  }
}