<?php
class CategoryEditScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    $currentCategory = end($_GET['category']);
    echo '<form method="POST" action=".">';
    echo '<input type="hidden" name="_method" value="PUT">';
    echo '<input type="hidden" name="type" value="category">';
    echo '<input name="id" type="hidden" value="'.$currentCategory['id'].'" />';
    echo '<input name="name" type="text" value="'.$currentCategory['name'].'" />';
    echo '<input type="submit" />';
    echo '</form>';
  }
}