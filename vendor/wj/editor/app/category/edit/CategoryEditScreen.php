<?php
class CategoryEditScreen extends Screen {
  public function renderContent() {
    //$breadcrumb = new Breadcrumb;
    //$breadcrumb->render();
    $currentCategory = Category::getById($_GET['id']);
    echo '<form method="POST" action=".">';
    echo '<input type="hidden" name="_method" value="PUT">';
    echo '<input type="hidden" name="type" value="category">';
    echo '<input name="id" type="hidden" value="'.$currentCategory->getId().'" />';
    echo '<input name="name" type="text" value="'.$currentCategory->getName().'" />';
    echo '<input type="submit" />';
    echo '</form>';
  }
}