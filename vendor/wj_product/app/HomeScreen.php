<?php
class HomeScreen extends Screen {
  public function renderContent() {
    $categories = DbCategory::getList();
    echo '<div id="category_list">';
    foreach ($categories as $category) {
      echo '<a href="'.urlencode($category['name']).'/">'
        .$category['name'].'</a> ';
    }
    echo '</div>';
  }
}