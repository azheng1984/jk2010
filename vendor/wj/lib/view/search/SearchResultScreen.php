<?php
class SearchResultScreen {
  public static function render($page) {
    echo '<div id="search"><div id="result">';
    SearchProductListScreen::render($page);
    echo '</div></div>';
  }
}