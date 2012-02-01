<?php
class SearchScreen extends Screen {
  public function __construct() {
    header('Cache-Control: private, max-age=0');
    $GLOBALS['URI']['RESULTS'] = ProductSearch::search();
  }

  protected function renderHtmlHeadContent() {
    echo '<title>', $GLOBALS['URI']['QUERY'], ' - 货比万家</title>';
    $this->addCssLink('search');
    $this->addJsLink('search');
  }

  protected function renderHtmlBodyContent() {
    echo '<div class="ad">';
    AdSenseScreen::render();
    echo '</div>';
    BreadcrumbScreen::render();
    echo '<div id="search">';
    ResultScreen::render();
    echo '</div>';
    echo '<div class="ad bottom">';
    AdSenseScreen::render();
    echo '</div>';
    RelatedQueryScreen::render();
  }
}