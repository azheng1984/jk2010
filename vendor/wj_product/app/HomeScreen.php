<?php
class HomeScreen extends Screen {
  public function renderContent() {
    $categories = DbCategory::getList();
    foreach ($categories as $category) {
      echo '<a href="'.urlencode($category['name']).'/">'
        .$category['name'].'</a> ';
    }
  }
}