<?php
class CategoryEditAction {
  public function GET() {}

  public function PUT() {
    if (!is_array($GLOBALS['PATH_SECTION_LIST'][1])) {
      throw new Exception('url 错误');
    }
    if (Db::getColumn(
      'SELECT count(*) FROM category WHERE name = ?', $_POST['name']) !== '0'
    ) {
      echo '分类已存在';
      return;
    }
    $categoryId = $GLOBALS['PATH_SECTION_LIST'][1][1];
    Db::update('category', array(
      'name' => $_POST['name'],
      'parent_id' => $_POST['parent_id'],
    ), 'id = ?', $categoryId);
    $GLOBALS['APP']->redirect('http://dev.youxuanji.com/category-'.$categoryId.'/');
  }
}