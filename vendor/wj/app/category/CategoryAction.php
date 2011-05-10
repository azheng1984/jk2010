<?php
class CategoryAction {
  public function DELETE() {
    
  }

  public function POST() {
    if ($_POST['original_name']) {
      //move name to alias
      //execute update
    } else {
      if (isset($_GET['category'])) {
        $currentCategory = end($_GET['category']);
        Category::save($_POST['name'], $currentCategory['id']);
      } else {
        Category::save($_POST['name']);
      }
    }
  }

  public function GET() {}
}