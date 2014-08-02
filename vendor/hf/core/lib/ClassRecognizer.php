<?php
namespace Hyperframework;

class ClassRecognizer {
  public static function getName($fileName) {
    $pattern = '/^([A-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*).php$/';
    if (preg_match($pattern, $fileName)) {
      return preg_replace('/.php$/', '', $fileName);
    }
  }
}
