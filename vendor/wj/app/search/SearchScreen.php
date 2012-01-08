<?php
class SearchScreen extends Screen {
  public function __construct() {
    $GLOBALS['URI']['RESULTS'] = ProductSearch::search();
  }

  protected function renderHeadContent() {
    echo '<title>', $GLOBALS['URI']['QUERY'], ' - 货比万家</title>';
    $this->renderCssLink('search');
    $this->renderJsLink('search');
  }

  protected function renderBodyContent() {
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
    echo '<div id="top_ads_wrapper">';
    AdSenseScreen::render();
    echo '</div>';
  }

  private function renderBottomAdvertisement() {
    echo '<div id="bottom_ads_wrapper">';
    AdSenseScreen::render();
    echo '</div>';
  }

  private function renderRelatedQuery() {
    echo '<div id="related">';
    RelatedQueryScreen::render();
    echo '</div>';
  }
}