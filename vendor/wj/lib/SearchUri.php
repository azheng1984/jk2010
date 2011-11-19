<?php
class SearchUri {
  public static function parse() {
    list($path) = explode('?', $_SERVER['REQUEST_URI'], 2);
    $sections = explode('/', $path);
    array_shift($sections);
    array_pop($sections);
    $amount = count($sections);
    if ($amount === 0) {
      throw new NotFoundException();
    }
    $result = array('query' => $sections[0]);
    if ($amount > 1) {
      $result['category'] = $sections[1];
    }
    if ($amount > 2) {
      $result['properties'] = explode('&', $sections[2]);
    }
    return $result;
  }
}