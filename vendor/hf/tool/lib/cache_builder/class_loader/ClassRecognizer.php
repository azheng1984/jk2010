<?php
class ClassRecognizer {
  public function getClasses($files) {
    $classes = array();
    if (count($files) !== 0) {
      foreach ($files as $file) {
        if (
          preg_match('/^[A-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*.php$/', $file)
        ) {
          $classes[] = preg_replace('/.php$/', '', $file);
        }
      }
    }
    return $classes;
  }
}