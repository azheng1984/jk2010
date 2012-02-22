<?php
class SearchResultScreen {
  public static function render() {
    echo '<div id="search"><div id="result"><div id="result_header">';
    SearchSortScreen::render();
    self::renderTotalFound();
    echo '</div>';
    SearchProductListScreen::render();
    PaginationScreen::render($GLOBALS['PAGE'],
      $GLOBALS['SEARCH_RESULT']['total_found'], $GLOBALS['QUERY_STRING']);
    echo '</div></div>';
  }

  private static function renderTotalFound() {
    echo '<div id="total_found">找到 ',
      $GLOBALS['SEARCH_RESULT']['total_found'], ' 个商品</div>';
  }
}