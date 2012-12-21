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
      $title .= '_同款';
    }
    if (isset($GLOBALS['CATEGORY'])) {
      $title .= '/'.$GLOBALS['CATEGORY']['name'];
    }
    if (isset($GLOBALS['PROPERTY_LIST'])) {
      $title .= '/'.urldecode($GLOBALS['PATH_SECTION_LIST'][3]);
    }
    echo '<title>', htmlentities($title, ENT_NOQUOTES, 'UTF-8'),
      ' 价格、品牌';
    if ($GLOBALS['PAGE'] > 1) {
      echo '(', $GLOBALS['PAGE'], ')';
    }
    echo ' - 货比万家</title>';
    $this->addCssLink('search');
    $this->addJsLink('search');
    if ($GLOBALS['SEARCH_RESULT'] === false
      || $GLOBALS['SEARCH_RESULT']['total_found'] === 0) {
      echo '<meta name="robots" content="noindex"/>';
    }
  }

  protected function renderHtmlBodyContent() {
    AdSenseScreen::render('1');
    SearchNavigationScreen::render();
    echo '<div id="search"><div id="result_wrapper" class="content">';
    $this->renderResult();
    echo '</div></div>';
    AdSenseScreen::render('2', 'ad bottom');
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
      SearchToolbarScreen::render();
      echo '<div id="no_result"><div>没有找到相关商品，建议：</div>',
        '<ul><li>检查搜索条件是否有误</li>',
        '<li>扩大搜索范围</li><li>返回 <a href="/" rel="nofollow">首页</a> 重新开始</li>',
        '<li><a href="http://www.taobao.com/" rel="nofollow" target="_blank">淘宝网搜索: "', $GLOBALS['QUERY']['name'], '"</a></li></ul></div>';
      return;
    }
    SearchResultScreen::render();
  }
}
