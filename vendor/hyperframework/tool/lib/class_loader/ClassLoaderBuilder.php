<?php
class ClassLoaderBuilder {
  public function getName($file) {
    $pattern = '/^([A-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*).php$/';
    if (preg_match($pattern, $file)) {
      return preg_replace('/.php$/', '', $file);
    }
  }
}