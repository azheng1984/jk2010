<?php
class SearchUriParser {
  public static function parse() {
    $depth = count($GLOBALS['URI']['PATH_SECTION_LIST']);
    if ($depth < 3 || $depth > 5) {
      throw new NotFoundException;
    }
    /* /query/ */
    self::parseQuery();
    /* /query/category/ */
    if ($depth > 3) {
      self::parseCategory();
    }
    /* /query/category/properties/ */
    if ($depth === 5) {
      SearchPropertyUriParser::parse();
    }
    self::parsePage();
    self::parseMediaType();
    return '/search';
  }

  private static function parseQuery() {
    $GLOBALS['URI']['QUERY'] =
      urldecode($GLOBALS['URI']['PATH_SECTION_LIST'][1]);
  }

  private static function parseCategory() {
    $name = urldecode($GLOBALS['URI']['PATH_SECTION_LIST'][2]);
    $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName($name);
    if ($GLOBALS['URI']['CATEGORY'] === false) {
      $GLOBALS['URI']['CATEGORY'] = array('name' => $name);
    }
  }

  private static function parsePage() {
    $section = end($GLOBALS['URI']['PATH_SECTION_LIST']);
    if ($section === '') {
      return;
    }
    if (!is_numeric($section)) {
      throw new NotFoundException;
    }
    $GLOBALS['URI']['PAGE'] = $section;
  }

  private static function parseMediaType() {
    if (isset($_GET['media']) && $_GET['media'] === 'json') {
      $_SERVER['REQUEST_MEDIA_TYPE'] = 'Json';
    }
  }
}