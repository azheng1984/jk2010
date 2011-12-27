<?php
class SearchPropertyUriParser {
  private static $key = false;
  private static $valueList;

  /* key=value&key=value&!value&key=!value */
  public static function parse() {
    $GLOBALS['URI']['PROPERTY_LIST'] = array();
    $blockList = explode('&', $GLOBALS['URI']['PATH_SECTION_LIST'][3]);
    foreach ($blockList as $block) {
      $list = explode('=', $block, 2);
      if (count($list) === 2) {
        self::moveNextKey(array_shift($list));
      }
      if (self::$key === false) {
        throw new NotFoundException;
      }
      $valueUriName = $list[0];
      $isInclude = true;
      if (strpos($valueUriName, '!') === 0) {
        $isInclude = false;
        $valueUriName = substr($valueUriName, 1);
      }
      $this->addValue($valueUriName, $isInclude);
    }
    self::moveNextKey();
  }

  private static function moveNextKey($keyUriName = null) {
    if ($keyUriName === '') {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['PROPERTY_LIST'][] =
      array('KEY' => self::$key, 'VALUES' => self::$valueList);
    if ($keyUriName === null) {
      return;
    }
    self::$key = false;
    self::$values = array();
    $keyName = urldecode($keyUriName);
    if (isset($GLOBALS['URI']['CATEGORY']['id'])) {
      self::$key = DbPropertyKey::getByName(
        $GLOBALS['URI']['CATEGORY']['id'], $keyName
      );
    }
    if (self::$key === false) {
      self::$key = array('name' => $keyName);
    }
  }

  private static function addValue($valueUriName, $isInclude) {
    if ($valueUriName === '') {
      throw new NotFoundException;
    }
    $value = false;
    $valueName = urldecode($valueUriName);
    if (isset(self::$key['id'])) {
      $value = DbPropertyValue::getByName(
        self::$key['id'], urldecode($valueUriName)
      );
    }
    if ($value === false) {
      $value = array('name' => $valueName);
    }
    $value['is_include'] = $isInclude;
    self::$valueList[] = $value;
  }
}