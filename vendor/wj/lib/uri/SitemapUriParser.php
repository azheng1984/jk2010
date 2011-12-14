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
    $GLOBALS['URI']['KEY_INDEXES'] = KeyIndexSearch::search(25);
    $GLOBALS['URI']['QUERY_INDEXES'] = QueryIndexSearch::searchByCategoryId(25);
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name']).'/';
    return '/category';
  }

  private static function parseIndex() {
    $GLOBALS['URI'] = array();
    $arguments = self::parseIndexArguments();
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
    if (count($arguments) !== 0) {
      $GLOBALS['URI']['STANDARD'] .= '?'.implode('&', $arguments);
    }
    return '/index';
  }

  private static function parseCategoryIndex() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'category';
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(urldecode(self::$sections['2'])); //caution security!
    $GLOBALS['URI']['INDEXES'] = CategoryIndexSearch::search();
    $GLOBALS['URI']['STANDARD'] = '/+i/';
  }

  private static function parseQueryIndexByCategory() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'query_by_category';
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(urldecode(self::$sections['2'])); //caution security!
    $GLOBALS['URI']['INDEXES'] = QueryIndexSearch::searchByCategoryId();
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name']);
  }

  private static function parseKeyIndex() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'key';
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(urldecode(self::$sections['2'])); //caution security!
    $GLOBALS['URI']['KEY'] = DbProperty::getKeyByName($GLOBALS['URI']['CATEGORY']['id'], self::$sections['3']); //caution security!
    $GLOBALS['URI']['INDEXES'] = KeyIndexSearch::search();
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name']).'/';
  }

  private static function parseValueIndex() {
    $GLOBALS['URI']['INDEX_TYPE'] = 'value';
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(urldecode(self::$sections['2'])); //caution security!
    $GLOBALS['URI']['KEY'] = DbProperty::getKeyByName($GLOBALS['URI']['CATEGORY']['id'], self::$sections['3']); //caution security!
    $GLOBALS['URI']['INDEXES'] = ValueIndexSearch::search();
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name'])
      .'/'.urlencode($GLOBALS['URI']['KEY']['name']).'/';
  }

  private static function parseQueryIndexByValue() {
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(urldecode(self::$sections['2'])); //caution security!
    $GLOBALS['URI']['KEY'] = DbProperty::getKeyByName($GLOBALS['URI']['CATEGORY']['id'], self::$sections['3']); //caution security!
    $GLOBALS['URI']['VALUE'] = DbProperty::getValueByName($GLOBALS['URI']['KEY']['id'], self::$sections['4']); //caution security!
    $GLOBALS['URI']['INDEXES'] = QueryIndexSearch::searchByValueId();
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name'])
      .'/'.urlencode($GLOBALS['URI']['KEY']['name'])
      .'/'.urlencode($GLOBALS['URI']['VALUE']['name']);
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
    return $arguments;
  }
}