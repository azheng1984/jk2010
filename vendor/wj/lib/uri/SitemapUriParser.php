<?php
class SitemapUriParser {
  private static $sections;

  public static function parse($sections) {
    self::$sections = $sections;
    if ($sections[1] !== '+i') {
      throw new NotFoundException;
    }
    if (count($sections) === 4
      && $sections[3] === ''
      && !isset($_GET['page'])
      && !isset($_GET['index'])) {
        return self::parseCategory();
    }
    return self::parseIndex();
  }

  private static function parseCategory() {
    $GLOBALS['URI'] = array();
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(urldecode(self::$sections['2'])); //caution security!
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name']).'/';
    return '/category';
  }

  private static function parseIndex() {
    $GLOBALS['URI'] = array();
    $sectionAmount = count(self::$sections);
    if ($sectionAmount === 3) {
      if (self::$sections[2] === '') {
        self::parseCategoryIndex();
      } else {
        self::parseQueryIndexByCategory();
      }
    } elseif ($sectionAmount === 4
      && self::$sections[3] === ''
      && (isset($_GET['page']) || isset($_GET['index']))) {
      self::parseKeyIndex();
    } elseif ($sectionAmount === 5) {
      if (self::$sections[4] === '') {
        self::parseValueIndex();
      } else {
        self::parseQueryIndexByValue();
      }
    } else {
      throw new NotFoundException;
    }
    self::parseIndexArguments();
    return '/index';
  }

  private static function parseCategoryIndex() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'category';
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(urldecode(self::$sections['2'])); //caution security!
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name']);
  }

  private static function parseQueryIndexByCategory() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'query_by_category';
    $GLOBALS['URI']['STANDARD'] = '/+i/';
  }

  private static function parseKeyIndex() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'key';
  }

  private static function parseValueIndex() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'value';
  }

  private static function parseQueryIndexByValue() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'query_by_value';
  }

  private static function parseIndexArguments() {
    $arguments = array();
    if (isset($_GET['index'])) {
      $GLOBALS['URI']['INDEX'] = $_GET['index'];
      $arguments[] = 'index='.$_GET['index'];
    }
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
      $GLOBALS['URI']['PAGE'] = $_GET['page'];
      $arguments[] = 'page='.$_GET['page'];
    }
    if (count($arguments) !== 0) {
      $GLOBALS['URI']['STANDARD'] .= '?'.implode('&', $arguments);
    }
  }
}