<?php
class ApplicationConfiguration {
  public function extract($config) {
    $analyzers = array();
    foreach ($this->config as $key => $value) {
      if (is_int($key)) {
        $analyzers[$value] = new $value.'Analyzer';
      }
      $class = $key.'Analyzer';
      $analyzers[$key] = new $class($value);
    }
    return $analyzers;
  }
}