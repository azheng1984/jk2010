<?php
class SearchUriParser {
  private static $sections;

  public static function parse($sections) {
  if (isset($_GET['q'])) {
      $location = $_GET['q'] === '' ? '' : $_GET['q'].'/';
      return $this->redirect('/'.$location);
    }
    self::$sections = $sections;
    $amount = count($sections);
    if ($amount === 0 || $sections[0] === '') {
      throw new NotFoundException;
    }
    self::parseQuery();
    if ($amount > 1) {
      self::parseCategory();
    }
    if ($amount > 2) {
      self::parseProperties();
    }
    self::parseArguments();
    $GLOBALS['URI']['RESULTS'] = ProductSearch::search();
  }

  private static function parseQuery() {
    $GLOBALS['URI']= array('QUERY' => urldecode(self::$sections[0]));
    $GLOBALS['URI']['STANDARD'] = '/'.self::$sections[0].'/';
  }

  private static function parseCategory() {
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(
      urldecode(self::$sections[1])
    );
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD'] .= self::$sections[1].'/';
  }

  //key=value&key=value&!value&key=!value
  private static function parseProperties() {
    $properties = array();
    $key = false;
    $values = null;
    $items = explode('&', self::$section[2]);
    foreach ($items as $item) {
      $tmps = explode('=', $item, 2);
      if (count($tmps) === 2) {
        if ($key !== false && count($values) !== 0) {
          $properties[] = array('KEY' => $key, 'VALUES' => $values);
        }
        $key = DbProperty::getKeyByName(
          $GLOBALS['URI']['CATEGORY']['id'], urldecode(array_shift($tmps))
        );
        $values= array();
        $valueArguments = array();
      }
      $valueName = $tmps[0];
      $isInclude = true;
      if (substr($valueName, 0, 1) === '!') {
        $isInclude = false;
        $valueName = substr($valueName, 1);
      }
      $value = DbProperty::getValueByName($key['id'], urldecode($valueName));
      $value['is_include'] = $isInclude;
      if ($value != false) {
        $values[] = $value;
      }
    }
    if ($key !== false && count($values) !== 0) {
      $properties[] = array('KEY' => $key, 'VALUES' => $values);
    }
    if (count($properties) !== 0) {
      $GLOBALS['URI']['PROPERTIES'] = $properties;
      self::restoreProperties();
    }
  }

  private static function restoreProperties() {
    $items = array();
    foreach ($GLOBALS['URI']['PROPERTIES'] as $property) {
      $item = urlencode($property['KEY']['name'])
        .'='.self::restorePropertyValues($property['VALUES']);
      $items[$property['KEY']['uri_index']] = $item;
    }
    ksort($items);
    $GLOBALS['URI']['STANDARD'] .= implode('&', $items);
  }

  private static function restorePropertyValues($values) {
    $items = array();
    foreach ($values as $value) {
      $item = urlencode($value['name']);
      if (!$value['is_include']) {
        $item = '!'.$item;
      }
      $items[$value['uri_index']] = $item;
    }
    ksort($items);
    return implode('&', $items);
  }

  private static function parseArguments() {
    $arguments = array();
    if (isset($_GET['key']) && isset($GLOBALS['URI']['CATEGORY'])) {
      $GLOBALS['URI']['KEY'] = DbProperty::getKeyByName(
        $GLOBALS['URI']['CATEGORY']['id'], $_GET['key']
      );
      $arguments[] = 'key='.urlencode($_GET['key']);
    }
    if (isset($_GET['media']) && $_GET['media'] === 'json') {
      $_SERVER['REQUEST_MEDIA_TYPE'] = 'Json';
      $arguments[] = 'media=json';
    }
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      $GLOBALS['URI']['MODEL_ID'] = $_GET['id'];
      $arguments[] = 'id='.$_GET['id'];
    }
    if (isset($_GET['price_from']) && is_numeric($_GET['price_from'])) {
      $GLOBALS['URI']['PRICE_FROM'] = $_GET['price_from'];
      $arguments[] = 'price_from='.$_GET['price_from'];
    }
    if (isset($_GET['price_to']) && is_numeric($_GET['price_to'])) {
      $GLOBALS['URI']['PRICE_TO'] = $_GET['price_to'];
      $arguments[] = 'price_to='.$_GET['price_to'];
    }
    if (isset($_GET['sort'])) {
      $GLOBALS['URI']['SORT'] = $_GET['sort'];
      $arguments[] = 'sort='.urlencode($_GET['sort']);
    }
    if (count($arguments) > 0) {
      $GLOBALS['URI']['STANDARD'] .= '?'.implode('&', $arguments);
    }
  }
}