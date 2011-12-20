<?php
class SitemapUriParser {
  private static $sections;

  public static function parse($sections) {
    self::$sections = $sections;
    if ($sections[1] !== '+i') {
      throw new NotFoundException;
    }
    $GLOBALS['URI'] = array();
    if (count($sections) === 4 && $sections[3] === ''
      && !isset($_GET['page']) && !isset($_GET['index'])) {
        return self::parseCategory();
    }
    return self::parseLinkList();
  }

  private static function parseCategory() {
    self::parsePropertyKeyLinkList();
    $GLOBALS['URI']['KEY_LIST'] = KeyLinkSearch::search(25);
    $GLOBALS['URI']['QUERY_LIST'] = QueryLinkSearch::searchByCategory(25);
    return '/category';
  }

  private static function parseLinkList() {
    $arguments = self::parseArguments();
    $sectionAmount = count(self::$sections);
    if ($sectionAmount === 3) {
      if (self::$sections[2] === '') {
        self::parseCategoryLinkList();
        $GLOBALS['URI']['LINK_LIST'] = CategoryLinkSearch::search();
      } else {
        self::parseQueryLinkListByCategory();
        $GLOBALS['URI']['LINK_LIST'] = QueryLinkSearch::searchByCategory();
      }
    } elseif ($sectionAmount === 4
      && self::$sections[3] === ''
      && (isset($_GET['page']) || isset($_GET['index']))) {
      self::parsePropertyKeyLinkList();
      $GLOBALS['URI']['LINK_LIST'] = KeyLinkSearch::search();
    } elseif ($sectionAmount === 5) {
      if (self::$sections[4] === '') {
        self::parsePropertyValueLinkList();
        $GLOBALS['URI']['LINK_LIST'] = ValueLinkSearch::search();
      } else {
        self::parseQueryLinkListByValue();
        $GLOBALS['URI']['LINK_LIST'] = QueryLinkSearch::searchByPropertyValue();
      }
    } else {
      throw new NotFoundException;
    }
    if (count($arguments) !== 0) {
      $GLOBALS['URI']['STANDARD'] .= '?'.implode('&', $arguments);
    }
    return '/link_list';
  }

  private static function parsePage() {
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

  private static function parseCategoryLinkList() {
    $GLOBALS['URI']['STANDARD'] = '/+i/';
  }

  private static function parseQueryLinkListByCategory() {
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(
      urldecode(self::$sections['2'])
    ); //caution security!
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD'] = '/+i/'.urlencode($GLOBALS['URI']['CATEGORY']['name']);
  }

  private static function parsePropertyKeyLinkList() {
    self::parseQueryLinkListByCategory();
    $GLOBALS['URI']['STANDARD'] .= '/';
  }

  private static function parsePropertyValueLinkList() {
    self::parsePropertyKeyLinkList();
    $GLOBALS['URI']['KEY'] = DbProperty::getKeyByName(
      $GLOBALS['URI']['CATEGORY']['id'], self::$sections['3']
    ); //caution security!
    if ($GLOBALS['URI']['KEY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD'] .= urlencode($GLOBALS['URI']['KEY']['name']).'/';
  }

  private static function parseQueryLinkListByValue() {
    self::parsePropertyValueLinkList();
    $GLOBALS['URI']['VALUE'] = DbProperty::getValueByName(
      $GLOBALS['URI']['KEY']['id'], self::$sections['4']
    ); //caution security!
    if ($GLOBALS['URI']['VALUE'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD'] .= '/'.urlencode($GLOBALS['URI']['VALUE']['name']);
  }
}