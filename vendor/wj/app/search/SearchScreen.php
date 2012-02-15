<?php
class SearchScreen extends Screen {
  public function __construct() {
    SearchQueryString::parse();
    if ($GLOBALS['PAGE'] > 50) {
      throw new NotFoundException;
    }
    $GLOBALS['SEARCH_RESULT'] = ProductSearchService::search();
    $this->verifyPagination();
  }

  protected function renderHtmlHeadContent() {
    $title = $GLOBALS['QUERY']['name'];
    if (isset($GLOBALS['IS_RECOGNITION'])) {
      $title .= '(同款)';
    }
    if (isset($GLOBALS['CATEGORY'])) {
      $title .= '/'.$GLOBALS['CATEGORY']['name'].'/';
    }
    if (isset($GLOBALS['PROPERTY_LIST'])) {
      $title .= urldecode($GLOBALS['PATH_SECTION_LIST'][3]).'/';
    }
    echo '<title>', htmlentities($title, ENT_NOQUOTES, 'UTF-8'),
      '价格、折扣、销量排行';
    if ($GLOBALS['PAGE'] > 1) {
      echo '(', $GLOBALS['PAGE'], ')';
    }
    echo '-货比万家</title>';
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

  private function verifyPagination() {
    if ($GLOBALS['PAGE'] === 1
      || isset($GLOBALS['SEARCH_RESULT']['matches']) !== false) {
      return;
    }
    $this->stop();
    header('HTTP/1.1 301 Moved Permanently');
    Header('Location: .'.$GLOBALS['QUERY_STRING']);
    return;
  }

  private function renderResult() {
    if ($GLOBALS['SEARCH_RESULT'] === false
      || $GLOBALS['SEARCH_RESULT']['total_found'] === 0) {
      echo '<div id="no_result"><h2>没有找到相关商品，建议:</h2>',
        '<ul><li>检查搜索条件是否有误</li>',
        '<li>扩大搜索范围</li><li>去 <a href="/">商店列表</a> 逛逛</li></ul></div>';
      return;
    }
    SearchResultScreen::render($this);
  }
}