<?php
class SearchUriParser {
  public static function parse() {
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
    self::parseMediaType();
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

  private static function parseMediaType() {
    if (isset($_GET['media']) && $_GET['media'] === 'json') {
      $_SERVER['REQUEST_MEDIA_TYPE'] = 'Json';
    }
  }
}