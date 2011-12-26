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
      $GLOBALS['URI']['LINK_LIST'] = DbCategory::getList();
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
      $GLOBALS['URI']['KEY_LINK_LIST'] = DbPropertyKey::getList(25);
      $GLOBALS['URI']['QUERY_LINK_LIST'] =
        QuerySearch::searchByCategory(25);
      return '/category';
    }
    /* /+i/category/+k/ */
    if ($depth === 5 && $sectionList[3] === '+k') {
      $GLOBALS['URI']['LINK_LIST'] = DbPropertyKey::getList();
      return '/link_list';
    }
    /* /+i/category/+q/ */
    if ($depth === 5 && $sectionList[3] === '+q') {
      $GLOBALS['URI']['LINK_LIST'] = QuerySearch::searchByCategory();
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
      $GLOBALS['URI']['LINK_LIST'] = DbPropertyValue::getList();
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
      $GLOBALS['URI']['LINK_LIST'] = QuerySearch::searchByPropertyValue();
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
    $GLOBALS['URI']['INDEX'] = $list[0];
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