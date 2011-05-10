<?php
class CategoryNewScreen extends Screen {
  public function renderContent() {
    echo '<form method="POST" action="." onsubmit="return getAction(this)">';
    echo '<input name="type" type="hidden" value="category" />';
    echo '<input name="name" type="text" value="" />';
    echo '<input type="submit" />';
    echo '</form>';
  }
}