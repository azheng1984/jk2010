<?php
class CategoryListScreen extends Screen {
  private $category;

  public function __construct() {
    $this->category = end($_GET['categories']);
  }

  protected function renderHeadContent() {
    echo '<title>'.$this->category['name'].' - 货比万家</title>';
    echo '<link type="text/css" href="/css/home.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<link type="text/css" href="/css/category_list.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  protected function renderBodyContent() {
    $breadcrumb = new Breadcrumb($_GET['categories']);
    $breadcrumb->render();
    echo '<div id="h1_wrapper"><h1>', $this->category['name'], '</h1></div>';
    echo '<div id="category_list_warpper">';
    CategoryListContentScreen::render($this->category['id']);
    echo '</div>';
    $this->renderAds();
  }

  private function renderAds() {
    echo '<div id="bottom_ads_wrapper"><div id="bottom_ads">';
    //AdSenseScreen::render();
    AdSenseScreen::render(true);
    echo '</div></div>';
  }
}