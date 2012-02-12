<?php
class IndexScreen extends Screen {
  private $category;
  private $page;

  public function __construct() {
    $depth = count($GLOBALS['PATH_SECTION_LIST']);
    $this->parseCategory($depth);
    $this->parsePage($depth);
  }

  protected function renderHtmlHeadContent() {

  }

  protected function renderHtmlBodyContent() {

  }

  private function parsePage($depth) {
    $pageSection = $GLOBALS['PATH_SECTION_LIST'][$depth - 1];
    if ($pageSection === '') {
      $this->page = 1;
      return;
    }
    if (!is_numeric($pageSection) || $pageSection < 2) {
      throw new NotFoundException;
    }
    $this->page = $pageSection;
  }

  private function parseCategory($depth) {
    /* /+i/ */
    if ($depth === 3) {
      $this->category = null;
      return;
    }
    /* /+i/category/ */
    $this->category = DbCategory::getByName(
      urldecode($GLOBALS['PATH_SECTION_LIST']['2'])
    );
    if ($this->category === false || $depth > 4) {
      throw new NotFoundException;
    }
  }
}