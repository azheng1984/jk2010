<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    foreach (Category::getList($GLOBALS['category']) as $row) {
      echo '<div><a href="'.urlencode($row['name']).'/">'.$row['name'].'</a></div>';
    }
  }
}