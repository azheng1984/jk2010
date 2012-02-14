<?php
class SearchScreen extends Screen {
  public function __construct() {
    SearchQueryString::parse();
  }

  protected function renderHtmlHeadContent() {
    $title = $GLOBALS['QUERY'];
    if (isset($GLOBALS['CATEGORY'])) {
      $title .= '/'.$GLOBALS['CATEGORY']['name'].'/';
    }
    if (isset($GLOBALS['PROPERTY_LIST'])) {
      $title .= urldecode($GLOBALS['PATH_SECTION_LIST'][3]).'/';
    }
    $title = htmlentities($title, ENT_NOQUOTES, 'UTF-8').'价格、折扣、销量排行';
    if ($GLOBALS['PAGE'] > 1) {
      $title .= '('.$GLOBALS['PAGE'].')';
    }
    echo '<title>', $title, '-货比万家</title>';
    $this->addCssLink('search');
    $this->addJsLink('search');
  }

  protected function renderHtmlBodyContent() {
    SearchAdSenseScreen::render('1');
    SearchBreadcrumbScreen::render();
    SearchResultScreen::render();
    SearchAdSenseScreen::render('2', 'ad bottom');
    $this->addJs($GLOBALS['SEARCH_PRODUCT_LIST_METADATA']);
  }

  private function getPropertyListTitle() {
    $title .= '/'.urldecode($GLOBALS['PATH_SECTION_LIST'][3]);
  }
}