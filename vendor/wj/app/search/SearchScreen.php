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
      echo '<div id="no_result"><h2>没有找到相关商品，建议:</h2>',
        '<ul><li>检查搜索条件是否有误</li>',
        '<li>扩大搜索范围</li><li><a href="/">去商店列表逛逛</a></li></ul></div>';
      return;
    }
    SearchResultScreen::render($this);
  }
}