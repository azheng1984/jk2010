<?php
class CategoryAction {
  public function DELETE() {
    
  }

  public function POST() {
    if (isset($_POST['id'])) {
      //move name to alias
      //execute update
    } else {
      $parentId = null;
      if (isset($_GET['category'])) {
        Category::save($_POST['name'], $_POST['parent_id']);
        $parentId = $_POST['parent_id'];
      } else {
        $_GET['category'] = array();
        Category::save($_POST['name']);
      }
      $_GET['category'][] = Category::get($_POST['name'], $parentId);
    }
  }

  public function GET() {}
}