<?php
class CategoryNewScreen extends Screen {
  public function renderContent() {
    echo '<form action="." method="POST">';
    echo '<input name="parent_id" type="hidden" value="'.$_GET['parent_id'].'" />';
    echo '<input name="name" type="text" value="" />';
    echo '<input type="submit" />';
    echo '</form>';
  }
}