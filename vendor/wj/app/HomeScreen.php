<?php
class HomeScreen extends Screen {
  public function renderContent() {
    $categories = DbCategory::getList();
    echo '<div id="home"><ul id="category_list" class="home_category_list">';
    for($i = 1; $i < 30; ++$i) {
      foreach ($categories as $category) {
        echo '<li><h2><a href="'.urlencode($category['name']).'/">'
          .$category['name'].'</a></h2>';
        $children = DbCategory::getList($category['id']);
        if (count($children) !== 0) {
          echo '<div class="children">';
          echo implode(' ', $this->getChildLinks($category, $children)).' ... </div>';
        }
        echo '</li>';
      }
    }
    echo '</ul></div>';
  }

  private function getChildLinks($category, $children) {
    $result = array();
    $parentLink = urlencode($category['name']).'/';
    for($i = 1; $i < 30; ++$i) {
      foreach ($children as $child) {
        $result[] = '<a href="'.$parentLink.urlencode($child['name']).'/">'.$child['name'].'</a>';
      }
    }
    return $result;
  }
}