<?php
class CategoryListScreen extends Screen {
  private $category;

  public function __construct() {
    $this->category = end($_GET['categories']);
  }

  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
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
    $this->renderCategoryList();
    echo '</div>';
    $this->renderAds();
  }

  private function renderCategoryList() {
    $categories = DbCategory::getList($this->category['id']);
    echo '<ul id="category_list">';
    foreach ($categories as $category) {
      for ($i = 0; $i < 15; ++$i) {
      echo '<li>';
      $this->renderCategory($category);
      echo '</li>';}
    }
    echo '</ul>';
  }

  private function renderCategory($category) {
    echo '<div class="item"><a rel="nofollow" href="'.urlencode($category['name']).'/">',
      $category['name'], '</a></div>';
    $children = DbCategory::getList($category['id']);
    //$children = array(array('name' => '笔记本电脑'), array('name' => '数码相机'));
    if (count($children) !== 0) {
      echo '<div class="children">',
        implode(' ', $this->getChildLinks($category, $children)),
        ' &hellip;</div>';
    }
  }

  private function getChildLinks($category, $children) {
    $result = array();
    $parentLink = urlencode($category['name']).'/';
    foreach ($children as $child) {
      $result[] = '<a rel="nofollow" href="'.$parentLink.urlencode($child['name']).'/">'
        .$child['name'].'</a>';
    }
    return $result;
  }

  private function renderAds() {
    echo '<div id="bottom_ads_wrapper"><div id="bottom_ads">Google 提供的广告</div></div>';
  }
}