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
    $this->parsePage($depth);
  }

  private function parseQuery() {
    $query = $GLOBALS['PATH_SECTION_LIST'][1];
    if (strpos($GLOBALS['PATH_SECTION_LIST'][1], '+-') === 0) {
      $GLOBALS['IS_RECOGNITION'] = true;
      $query = substr($GLOBALS['PATH_SECTION_LIST'][1], 2);
    }
    $query = trim(urldecode($query));
    $GLOBALS['QUERY'] = DbQuery::getByName($query);
    if ($GLOBALS['QUERY'] !== false) {
      return;
    }
    $GLOBALS['QUERY'] = array('name' => $query);
    if (isset($GLOBALS['IS_RECOGNITION'])) {
    }
  }

  private function parseCategory() {
    $name = urldecode($GLOBALS['PATH_SECTION_LIST'][2]);
    $GLOBALS['CATEGORY'] = DbCategory::getByName($name);
    if ($GLOBALS['CATEGORY'] === false) {
      $GLOBALS['CATEGORY'] = array('name' => $name);
    }
  }

  private function parsePropertyList() {
    $parser = new SearchPropertyListPathParser;
    $parser->parse();
  }

  private function parsePage($depth) {
    $path = $GLOBALS['PATH_SECTION_LIST'][$depth - 1];
    if ($path === '') {
      $GLOBALS['PAGE'] = 1;
      return;
    }
    if (!is_numeric($path) || $path < 2) {
      throw new NotFoundException;
    }
    $GLOBALS['PAGE'] = intval($path);
  }
}