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
    $GLOBALS['QUERY'] = trim(urldecode($GLOBALS['PATH_SECTION_LIST'][1]));
  }

  private function parseCatregory() {
    $name = urldecode($GLOBALS['PATH_SECTION_LIST'][2]);
    $GLOBALS['CATEGORY'] = DbCategory::getByName($name);
    if ($GLOBALS['CATEGORY'] === false) {
      $GLOBALS['CATEGORY'] = array('name' => $name);
    }
  }

  private function parsePropertyList() {
    $parser = new PropertyListPathParser;
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