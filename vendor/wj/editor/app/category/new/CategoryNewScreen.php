<?php
class CategoryNewScreen extends Screen {
  public function renderContent() {
    $parentId = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;
    echo '<form action="/category" method="POST">';
    echo '<input name="parent_id" type="hidden" value="'.$parentId.'" />';
    echo '<input name="name" type="text" value="" />';
    echo '<input type="submit" />';
    echo '</form>';
  }
}