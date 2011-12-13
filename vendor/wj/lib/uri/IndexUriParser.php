<?php
class IndexUriParser {
  public static function parse($sections) {
    if ($sections[1] !== '+i') {
      throw new NotFoundException;
    }
    if (count($sections) === 4
      && $sections[3] === ''
      && !isset($_GET['page'])
      && !isset($_GET['index'])) {
        return self::parseCategory($sections);
    }
    return self::parseIndex($sections);
  }

  private static function parseCategory($sections) {
    $GLOBALS['URI'] = array();
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(urldecode($sections['2'])); //caution security!
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name']).'/';
    return '/category';
  }

  private static function parseIndex($sections) {
    $GLOBALS['URI'] = array();
    $GLOBALS['URI']['STANDARD'] = $_SERVER['REQUEST_URI'];
    return '/index';
  }
}