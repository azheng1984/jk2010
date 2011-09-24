<?php
class HomeScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    echo '<link type="text/css" href="/css/home.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  protected function renderBodyContent() {
    $categories = DbCategory::getList();
    echo '<ul id="category_list">';
    foreach ($categories as $category) {
      echo '<li>';
      $this->renderCategory($category);
      echo '</li>';
    }
    echo '</ul>';
  }

  private function renderCategory($category) {
    echo '<h2><a href="'.urlencode($category['name']).'/">',
      $category['name'], '</a></h2>';
    $children = DbCategory::getList($category['id']);
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
      $result[] = '<a href="'.$parentLink.urlencode($child['name']).'/">'
        .$child['name'].'</a>';
    }
    return $result;
  }
}