<?php
class HomeScreen extends Screen {
  public function renderContent() {
    $categories = DbCategory::getList();
    print_r($categories);
    foreach ($categories as $category) {
      echo '<a href="数码/">'.$category['name'].'</a>';
    }
  }
}