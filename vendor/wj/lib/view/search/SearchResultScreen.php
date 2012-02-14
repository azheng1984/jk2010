<?php
class SearchResultScreen {
  public static function render($screen) {
    ProductSearch::search();
    echo '<div id="search"><div id="result">';
    SearchSortScreen::render();
    self::renderTotalFound();
    SearchProductListScreen::render($screen);
    $total = 1600;
    PaginationScreen::render($GLOBALS['PAGE'], $total);
    echo '</div></div>';
  }

  private static function renderTotalFound() {
    
  }
}