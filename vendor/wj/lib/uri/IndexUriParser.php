<?php
class IndexUriParser {
  public static function parse($sections) {
    if ($sections[1] !== '+i') {
      throw new NotFoundException;
    }
    if ($sections[2] === '') {
      return '/category_list';
    }
    return '/category';
  }
}