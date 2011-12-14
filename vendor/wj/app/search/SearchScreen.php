<?php
class SearchScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>', $GLOBALS['URI']['QUERY'], ' - 货比万家</title>';
    $this->renderCssLink('search');
    $this->renderCssLink('search_result');
    $this->renderCssLink('search_ads');
    $this->renderCssLink('search_result_sort');
    $this->renderCssLink('search_related');
    $this->renderCssLink('search_filter');
    $this->renderCssLink('search_suggestion');
    $this->renderJsLink('jquery-1.7.1');
    $this->renderJsLink('search');
    if ($GLOBALS['URI']['RESULTS']['total_found'] === 0) {
      echo '<meta name="robots" content="noindex, follow">';
    }
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