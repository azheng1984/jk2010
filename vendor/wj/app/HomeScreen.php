<?php
class HomeScreen extends Screen {
  public function renderContent() {
    $categories = DbCategory::getList();
    echo '<div id="product_search"><ul id="category_list" class="home_category_list">';
    foreach ($categories as $category) {
      echo '<li><a href="'.urlencode($category['name']).'/">'
        .$category['name'].'</a>';
      $children = DbCategory::getList($category['id']);
      if (count($children) !== 0) {
        echo '<div class="children">';
        echo '<div>'.implode('</div><div>', $this->getChildLinks($category, $children)).'</div>';
        echo '</div>';
      }
      echo '</li>';
    }
    echo '</ul></div>';
  }

  private function getChildLinks($category, $children) {
    $result = array();
    $parentLink = urlencode($category['name']).'/';
    foreach ($children as $child) {
      $result[] = '<a href="'.$parentLink.urlencode($child['name']).'/">'.$child['name'].'</a>';
    }
    return $result;
  }
}