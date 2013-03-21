<?php
class CategoryAction {
  public function GET() {}

  public function POST() {
    if (Db::getColumn('SELECT count(*) FROM category WHERE name = ?', $_POST['name']) !== '0') {
      echo '分类已存在';
      return;
    }
    Db::insert('category', array(
    'name' => $_POST['name'],
    'parent_id' => $_POST['parent_id'],
    ));
    $parentCategory = Db::getRow('SELECT * FROM category WHERE id = ?', $_POST['parent_id']);
    if ($parentCategory['is_leaf'] === '1') {
      Db::update('category', array('is_leaf' => 0), 'id = ?', $_POST['parent_id']);
    }
    if ($_POST['parent_id'] === '0') {
      header('Location: http://dev.youxuanji.com/');
    } else {
      header('Location: http://dev.youxuanji.com/category-'.$_POST['parent_id'].'/');
    }
    header('HTTP/1.1 302 Found');
  }
}