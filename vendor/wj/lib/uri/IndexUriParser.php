<?php
class IndexUriParser {
  public static function parse() {
    self::parseCategory();
    self::parsePage();
    return '/index';
  }

  private static function parsePage() {
    $section = end($GLOBALS['URI']['PATH_SECTION_LIST']);
    if ($section === '') {
      $GLOBALS['URI']['PAGE'] = '1';
      return;
    }
    if (is_numeric($section)) {
      $GLOBALS['URI']['PAGE'] = $section;
      return;
    }
    throw new NotFoundException;
  }

  private static function parseCategory() {
    $sectionList = $GLOBALS['URI']['PATH_SECTION_LIST'];
    $depth = count($sectionList);
    /* /+i/ */
    if ($depth === 3) {
      $GLOBALS['URI']['LIST_TYPE'] = 'category';
      return;
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
    throw new NotFoundException;
  }
}