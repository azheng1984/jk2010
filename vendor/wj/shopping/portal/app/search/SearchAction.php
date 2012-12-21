<?php
class SearchAction {
  public function GET() {
    $depth = count($GLOBALS['PATH_SECTION_LIST']);
    if ($depth < 3 || $depth > 5) {
      throw new NotFoundException;
    }
    /* /query/ */
    $this->parseQuery();
    /* /query/category/ */
    if ($depth > 3) {
      $this->parseCategory();
    }
    /* /query/category/property_list/ */
    if ($depth === 5) {
      $this->parsePropertyList();
    }
    PaginationParser::parsePath($GLOBALS['PATH_SECTION_LIST'][$depth - 1]);
  }

  private function parseQuery() {
    $query = $GLOBALS['PATH_SECTION_LIST'][1];
    if (strpos($GLOBALS['PATH_SECTION_LIST'][1], '+-') === 0) {
      $GLOBALS['IS_RECOGNITION'] = true;
      $query = substr($GLOBALS['PATH_SECTION_LIST'][1], 2);
    }
    $query = trim(urldecode($query));
    if ($query === '') {
      throw NotFoundException;
    }
    $GLOBALS['QUERY'] =
      Db::getRow('SELECT * FROM query WHERE name = ?', $query);
    if ($GLOBALS['QUERY'] !== false) {
      return;
    }
    $GLOBALS['QUERY'] = array('name' => $query);
  }

  private function parseCategory() {
    $name = trim(urldecode($GLOBALS['PATH_SECTION_LIST'][2]));
    if ($name === '') {
      throw NotFoundException;
    }
    $GLOBALS['CATEGORY'] =
      Db::getRow('SELECT * FROM category WHERE name = ?', $name);
    if ($GLOBALS['CATEGORY'] === false) {
      $GLOBALS['CATEGORY'] = array('name' => $name);
    }
  }
}