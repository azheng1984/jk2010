<?php
class SearchResultScreen {
  public static function render($searchScreen) {
    ProductSearch::search();
    echo '<div id="search"><div id="result">';
    SearchSortScreen::render();
    self::renderTotalFound();
    SearchProductListScreen::render($searchScreen);
    $total = 1600;
    PaginationScreen::render($GLOBALS['PAGE'], $total);
    echo '</div></div>';
  }

  private static function renderTotalFound() {
    echo '<div id="total_found">找到 ',
      $GLOBALS['SEARCH_RESULT']['total_found'], ' 个商品</div>';
  }
}