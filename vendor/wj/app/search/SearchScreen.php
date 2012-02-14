<?php
class SearchScreen extends Screen {
  public function __construct() {
    SearchQueryString::parse();
    $GLOBALS['SEARCH_RESULT'] = ProductSearch::search();
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
    $this->renderResult();
    SearchAdSenseScreen::render('2', 'ad bottom');
    SearchRelatedQueryScreen::render();
  }

  private function renderResult() {
    if ($GLOBALS['SEARCH_RESULT']['total_found'] === 0) {
      echo '<div id="empty_result">没有找到任何商品。</div>';
      return;
    }
    SearchResultScreen::render($this);
  }
}