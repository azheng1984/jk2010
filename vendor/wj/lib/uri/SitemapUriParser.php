<?php
class SitemapUriParser {
  public static function parse() {
    $pageSection = self::parsePage();
    $app = self::parseTag();
    $GLOBALS['URI']['STANDARD_PATH'] .= $pageSection;
    return $app;
  }

  private static function parseTag() {
    $sectionList = $GLOBALS['URI']['PATH_SECTION_LIST'];
    $amount = count($sectionList);
    $GLOBALS['URI']['STANDARD_PATH'] = '/+i';
    if ($amount === 3) {
      $GLOBALS['URI']['LINK_LIST'] = CategoryLinkSearch::search();
      return '/link_list';
    }
    /* /+i/category/ */
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(
      urldecode($sectionList['2'])
    );
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD_PATH'] .= '/'.$sectionList['2'];
    if ($amount === 4) {
      $GLOBALS['URI']['KEY_LINK_LIST'] = KeyLinkSearch::search(25);
      $GLOBALS['URI']['QUERY_LINK_LIST'] =
        QueryLinkSearch::searchByCategory(25);
      return '/category';
    }
    /* /+i/category/+k/ */
    if ($amount === 5
      && $GLOBALS['URI']['PATH_SECTION_LIST'][3] === '+k') {
      $GLOBALS['URI']['LINK_LIST'] = KeyLinkSearch::search();
      return '/link_list';
    }
    /* /+i/category/+q/ */
    if ($amount === 5
      && $GLOBALS['URI']['PATH_SECTION_LIST'][3] === '+q') {
      $GLOBALS['URI']['STANDARD_PATH'] .= '/+q';
      $GLOBALS['URI']['LINK_LIST'] = QueryLinkSearch::searchByCategory();
      return '/link_list';
    }
    /* /+i/category/key/ */
    $GLOBALS['URI']['KEY'] = DbProperty::getKeyByName(
      $GLOBALS['URI']['CATEGORY']['id'], $sectionList['3']
    );
    if ($GLOBALS['URI']['KEY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD_PATH'] .= '/'.$sectionList['3'];
    if ($amount === 5) {
      $GLOBALS['URI']['LINK_LIST'] = KeyLinkSearch::search();
      return '/link_list';
    }
    /* /+i/category/key/value/ */
    $GLOBALS['URI']['VALUE'] = DbProperty::getValueByName(
      $GLOBALS['URI']['KEY']['id'], $sectionList['4']
    );
    if ($GLOBALS['URI']['VALUE'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD_PATH'] .= '/'.$sectionList['4'];
    if ($amount === 6) {
      $GLOBALS['URI']['LINK_LIST'] = QueryLinkSearch::searchByPropertyValue();
      return '/link_list';
    }
    throw new NotFoundException;
  }

  private static function parsePage() {
    $section = end($GLOBALS['URI']['PATH_SECTION_LIST']);
    if ($section === '') {
      return '/';
    }
    $list = explode('-', $section, 2);
    $pageSection = '/'.$list[0];
    if (is_numeric($list[0])) {
      $GLOBALS['URI']['PAGE'] = $list[0];
      return $pageSection;
    }
    $GLOBALS['URI']['INDEX'] = $list[0];
    if (isset($list[1]) && is_numeric($list[1])) {
      $pageSection .= '-'.$list[1];
      $GLOBALS['URI']['PAGE'] = $list[1];
    }
    return $pageSection;
  }
}