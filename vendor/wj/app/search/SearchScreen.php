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
    $this->renderTopAdvertisement();
    $this->renderSearch();
    $this->renderBottomAdvertisement();
    $this->renderRelatedQuery();
  }

  private function renderSearch() {
    BreadcrumbScreen::render();
    echo '<div id="search">';
    ResultScreen::render();
    echo '</div>';
  }

  private function renderTopAdvertisement() {
    echo '<div class="ad">';
    AdSenseScreen::render();
    echo '</div>';
  }

  private function renderBottomAdvertisement() {
    echo '<div class="ad bottom">';
    AdSenseScreen::render();
    echo '</div>';
  }

  private function renderRelatedQuery() {
    echo '<div id="related">';
    RelatedQueryScreen::render();
    echo '</div>';
  }
}