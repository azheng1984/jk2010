<?php
class CategoryAction {
  public function GET() {}

  public function POST() {
    if (isset($_POST['id'])) { //put/delete
      if (isset($_POST['_method'] ) && $_POST['_method'] === 'DELETE') {
        $this->DELETE();
        return;
      }
      //move name to alias
      Category::update($_POST['id'], $_POST['name']);
    } else { //new
      $parentId = null;
      if (isset($_POST['parent_id']) && $_POST['parent_id'] !== '') {
        Category::save($_POST['name'], $_POST['parent_id']);
        $parentId = $_POST['parent_id'];
      } else {
        //$_GET['category'] = array();
        Category::save($_POST['name']);
      }
      if (isset($_POST['parent_id']) && $_POST['parent_id'] !== '') {
        //echo 'Location: /category?id='.$_POST['parent_id'];
        header('Location: /category?id='.$_POST['parent_id']);
      } else {
        header('Location: /');
      }
      exit;
    }
  }

  public function DELETE() {
    Category::delete($_POST['id']);
  }
}