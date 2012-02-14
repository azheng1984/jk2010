<?php
class SearchResultScreen {
  public static function render($searchScreen) {
    echo '<div id="search"><div id="result">';
    SearchSortScreen::render();
    self::renderTotalFound();
    SearchProductListScreen::render($searchScreen);
    PaginationScreen::render(
      $GLOBALS['PAGE'], $GLOBALS['SEARCH_RESULT']['total_found']
    );
    echo '</div></div>';
  }

  private static function renderTotalFound() {
    echo '<div id="total_found">找到 ',
      $GLOBALS['SEARCH_RESULT']['total_found'], ' 个商品</div>';
  }
}