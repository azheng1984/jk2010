<?php
class SitemapUriParser {
  public static function parse() {
    self::parsePage();
    return self::parseTag();
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
      return '/category';
    }
    /* /+i/category/+k/ */
    if ($depth === 5 && $sectionList[3] === '+k') {
      $GLOBALS['URI']['LIST_TYPE'] = 'key';
      return '/link_list';
    }
    /* /+i/category/+q/ */
    if ($depth === 5 && $sectionList[3] === '+q') {
      $GLOBALS['URI']['LIST_TYPE'] = 'query';
      return '/link_list';
    }
    /* /+i/category/key/ */
    $GLOBALS['URI']['KEY'] = DbProperty::getKeyByName(
      $GLOBALS['URI']['CATEGORY']['id'], $sectionList['3']
    );
    if ($GLOBALS['URI']['KEY'] === false) {
      throw new NotFoundException;
    }
    if ($depth === 5) {
      $GLOBALS['URI']['LIST_TYPE'] = 'value';
      return '/link_list';
    }
    /* /+i/category/key/value/ */
    $GLOBALS['URI']['VALUE'] = DbProperty::getValueByName(
      $GLOBALS['URI']['KEY']['id'], $sectionList['4']
    );
    if ($GLOBALS['URI']['VALUE'] === false) {
      throw new NotFoundException;
    }
    if ($depth === 6) {
      $GLOBALS['URI']['LIST_TYPE'] = 'query';
      return '/link_list';
    }
    throw new NotFoundException;
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
}