<?php
class IndexUriParser {
  public static function parse($sections) {
    if ($sections[1] !== '+i') {
      throw new NotFoundException;
    }
    if (count($sections) === 4) {
      return '/category';
    }
    return '/index';
  }
}