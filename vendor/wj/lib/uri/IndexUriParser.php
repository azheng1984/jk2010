<?php
class IndexUriParser {
  public static function parse($sections) {
    if ($sections[1] !== '+i') {
      throw new NotFoundException;
    }
    return '/category_list';
  }
}