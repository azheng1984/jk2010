<?php
class HomeScreen extends Screen {
  public function renderContent() {
    $categories = DbCategory::getList();
    echo '<div id="product_search"><ul id="category_list" class="home_category_list">';
    foreach ($categories as $category) {
      echo '<li><a href="'.urlencode($category['name']).'/">'
        .$category['name'].'</a></li>';
    }
    echo '</ul></div>';
  }
}