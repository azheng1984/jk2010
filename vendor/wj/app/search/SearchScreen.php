<?php
class SearchScreen extends Screen {
  private $productList;

  public function __construct() {
    $this->productList = ProductSearch::search();
  }

  protected function renderHtmlHeadContent() {
    $title = $GLOBALS['QUERY'];
    if (isset($GLOBALS['CATEGORY'])) {
      $title .= '/'.$GLOBALS['CATEGORY']['name'].'/';
    }
    if (isset($GLOBALS['PROPERTY_LIST'])) {
      $title .= urldecode($GLOBALS['PATH_SECTION_LIST'][3]).'/';
    }
    if ($GLOBALS['PAGE'] > 1) {
      $title .= '('.$GLOBALS['PAGE'].')';
    }
    echo '<title>', htmlentities($title, ENT_NOQUOTES, 'UTF-8'),
      '价格、折扣、销量排行-货比万家</title>';
    $this->addCssLink('search');
    $this->addJsLink('search');
  }

  protected function renderHtmlBodyContent() {
    BreadcrumbScreen::render();
  }

  private function getPropertyListTitle() {
    $title .= '/'.urldecode($GLOBALS['PATH_SECTION_LIST'][3]);
  }
}