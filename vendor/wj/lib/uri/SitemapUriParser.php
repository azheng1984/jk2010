<?php
class SitemapUriParser {
  public static function parse() {
    $pageSection = self::parsePage();
    $app = self::parseTag();
    $GLOBALS['URI']['STANDARD_PATH'] .= $pageSection;
    return $app;
  }

  private static function parseTag() {
    $sections = $GLOBALS['URI']['PATH_SECTION_LIST'];
    $amount = count($sections);
    $GLOBALS['URI']['STANDARD_PATH'] = '/+i';
    if ($amount === 3) {
      $GLOBALS['URI']['LINK_LIST'] = CategoryLinkSearch::search();
      return '/link_list';
    }
    /* /+i/category/ */
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(
      urldecode($sections['2'])
    );
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD_PATH'] .=
      '/'.urlencode($GLOBALS['URI']['CATEGORY']['name']);
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
      $GLOBALS['URI']['CATEGORY']['id'], $sections['3']
    );
    if ($GLOBALS['URI']['KEY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD_PATH'] .=
      '/'.urlencode($GLOBALS['URI']['KEY']['name']);
    if ($amount === 5) {
      $GLOBALS['URI']['LINK_LIST'] = KeyLinkSearch::search();
      return '/link_list';
    }
    /* /+i/category/key/value/ */
    $GLOBALS['URI']['VALUE'] = DbProperty::getValueByName(
      $GLOBALS['URI']['KEY']['id'], $sections['4']
    );
    if ($GLOBALS['URI']['VALUE'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD_PATH'] .=
      '/'.urlencode($GLOBALS['URI']['VALUE']['name']);
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
    $items = explode('-', $section, 2);
    $pageSection = '/'.$items[0];
    if (is_numeric($items[0])) {
      $GLOBALS['URI']['PAGE'] = $items[0];
      return $pageSection;
    }
    $GLOBALS['URI']['INDEX'] = $items[0];
    if (isset($items[1]) && is_numeric($items[1])) {
      $pageSection .= '-'.$items[1];
      $GLOBALS['URI']['PAGE'] = $items[1];
    }
    return $pageSection;
  }
}