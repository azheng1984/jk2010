<?php
class CategoryEditScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    $currentCategory = end($_GET['category']);
    echo '<form method="POST" action="."><input type="text" value="'.$currentCategory['name'].'" />';
    echo '<input type="submit" />';
    echo '</form>';
  }
}