<?php
class SearchUriParser {
  public static function parse() {
    if (isset($_GET['q']) && $GLOBALS['URI']['REQUEST_PATH'] === '/') {
      $location = $_GET['q'] === '' ? '' : $_GET['q'].'/';
      $GLOBALS['URI']['STANDARD_PATH'] = '/'.$location;
      return;
    }
    $amount = count($GLOBALS['URI']['PATH_SECTION_LIST']);
    /* /section */
    if ($amount < 3) {
      throw new NotFoundException;
    }
    /* /query/ */
    self::parseQuery();
    /* /query/category/ */
    if ($amount > 3) {
      self::parseCategory();
    }
    /* /query/category/properties/ */
    if ($amount === 5) {
      SearchPropertyUriParser::parse();
    }
    self::parsePage();
    self::parseParameters();
    $GLOBALS['URI']['RESULTS'] = ProductSearch::search();
    return '/search';
  }

  private static function parseQuery() {
    $GLOBALS['URI']['QUERY'] =
      urldecode($GLOBALS['URI']['PATH_SECTION_LIST'][1]);
    $GLOBALS['URI']['STANDARD_PATH'] =
      '/'.$GLOBALS['URI']['PATH_SECTION_LIST'][1];
  }

  private static function parseCategory() {
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(
      urldecode($GLOBALS['URI']['PATH_SECTION_LIST'][2])
    );
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD_PATH'] .=
      '/'.$GLOBALS['URI']['PATH_SECTION_LIST'][2];
  }

  private static function parsePage() {
    $section = end($GLOBALS['URI']['PATH_SECTION_LIST']);
    if ($section === '') {
      $GLOBALS['URI']['STANDARD_PATH'] .= '/';
      return;
    }
    if (!is_numeric($section)) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['STANDARD_PATH'] .= '/'.$section;
    $GLOBALS['URI']['PAGE'] = $section;
  }

  private static function parseParameters() {
    if (isset($_GET['key']) && isset($GLOBALS['URI']['CATEGORY'])) {
      $GLOBALS['URI']['KEY'] = DbProperty::getKeyByName(
        $GLOBALS['URI']['CATEGORY']['id'], $_GET['key']
      );
    }
    if (isset($_GET['media']) && $_GET['media'] === 'json') {
      $_SERVER['REQUEST_MEDIA_TYPE'] = 'Json';
    }
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      $GLOBALS['URI']['MODEL_ID'] = $_GET['id'];
    }
    if (isset($_GET['price_from']) && is_numeric($_GET['price_from'])) {
      $GLOBALS['URI']['PRICE_FROM'] = $_GET['price_from'];
    }
    if (isset($_GET['price_to']) && is_numeric($_GET['price_to'])) {
      $GLOBALS['URI']['PRICE_TO'] = $_GET['price_to'];
    }
    if (isset($_GET['sort'])) {
      $GLOBALS['URI']['SORT'] = $_GET['sort'];
    }
  }
}