<?php
class SearchScreen extends Screen {
  private $result;

  public function __construct() {
    $this->result = ProductSearch::search();
  }

  protected function renderHeadContent() {
    echo '<title>', $GLOBALS['URI']['QUERY'], ' - 货比万家</title>';
    $this->renderCssLink('search');
    $this->renderJsLink('jquery-1.7.1');
    $this->renderJsLink('search');
    if ($this->result['total_found'] === 0) {
      echo '<meta name="robots" content="noindex, follow">';
    }
  }

  protected function renderBodyContent() {
    //$this->renderTopAdvertisement();
    $this->renderSearch();
    //$this->renderBottomAdvertisement();
    $this->renderRelatedQuery();
  }

  private function renderSearch() {
    BreadcrumbScreen::render();
    echo '<div id="search">';
    ResultScreen::render($this->result);
    FilterScreen::render();
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