<?php
class SitemapUriParser {
  public static function parse() {
    self::parsePage();
    self::parseTag();
    return '/sitemap';
  }

  private static function parsePage() {
    $section = end($GLOBALS['URI']['PATH_SECTION_LIST']);
    if ($section === '') {
      return;
    }
    $list = explode('-', $section, 2);
    if (!isset($list[1]) && is_numeric($list[0])) {
      $GLOBALS['URI']['PAGE'] = $list[0];
      return;
    }
    $GLOBALS['URI']['ALPHABET_INDEX'] = $list[0];
    if (!isset($list[1])) {
      return;
    }
    if (is_numeric($list[1])) {
      $GLOBALS['URI']['PAGE'] = $list[1];
      return;
    }
    throw new NotFoundException;
  }

  private static function parseTag() {
    $sectionList = $GLOBALS['URI']['PATH_SECTION_LIST'];
    $depth = count($sectionList);
    if ($depth === 3) {
      $GLOBALS['URI']['LIST_TYPE'] = 'category';
      return '/link_list';
    }
    /* /+i/category/ */
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(
      urldecode($sectionList['2'])
    );
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      throw new NotFoundException;
    }
    if ($depth === 4) {
      $GLOBALS['URI']['LIST_TYPE'] = 'query';
      return;
    }
    /* /+i/category/+k/ */
    if ($depth === 5 && $sectionList[3] === '+k') {
      $GLOBALS['URI']['LIST_TYPE'] = 'key';
      return;
    }
    /* /+i/category/key/ */
    $GLOBALS['URI']['PROPERTY_KEY'] = DbPropertyKey::getByName(
      $GLOBALS['URI']['CATEGORY']['id'], urldecode($sectionList['3'])
    );
    if ($GLOBALS['URI']['PROPERTY_KEY'] === false) {
      throw new NotFoundException;
    }
    if ($depth === 5) {
      $GLOBALS['URI']['LIST_TYPE'] = 'value';
      return;
    }
    /* /+i/category/key/value/ */
    $GLOBALS['URI']['PROPERTY_VALUE'] = DbPropertyValue::getByName(
      $GLOBALS['URI']['KEY']['id'], urldecode($sectionList['4'])
    );
    if ($GLOBALS['URI']['PROPERTY_VALUE'] === false) {
      throw new NotFoundException;
    }
    if ($depth === 6) {
      $GLOBALS['URI']['LIST_TYPE'] = 'query';
      return;
    }
    throw new NotFoundException;
  }
}