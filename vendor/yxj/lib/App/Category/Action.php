<?php
class CategoryAction {
  public function GET() {}

  public function POST() {
    if (Db::getColumn(
      'SELECT count(*) FROM category WHERE name = ?', $_POST['name']) !== '0'
    ) {
      echo '分类已存在';//$GLOBAL['VIEW_DATA'] 传递给 view 显示 via View::setData($key, $value);
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
      $GLOBALS['APP']->redirect('http://dev.youxuanji.com/');
      return;
    }
    $GLOBALS['APP']->redirect(
      'http://dev.youxuanji.com/category-'.$_POST['parent_id'].'/'
    );
  }

  public function PUT() {
    if (!is_array($GLOBALS['PATH_SECTION_LIST'][1])) {
      throw new Exception('url 格式错误。');
    }
    $categoryId = $GLOBALS['PATH_SECTION_LIST'][1][1];
    Db::update('category', array(
      'name' => $_POST['name'],
      'parent_id' => $_POST['parent_id'],
    ), 'id = ?', $categoryId);
  }

  public function DELETE() {
    if (!is_array($GLOBALS['PATH_SECTION_LIST'][1])) {
      throw new Exception('url 格式错误。');
    }
    $categoryId = $GLOBALS['PATH_SECTION_LIST'][1][1];
    $category = Db::getRow('SELECT * FROM category WHERE id = ?', $categoryId);
    $this->disableCategory($category);
    if ($category['parent_id'] === '0') {
      $GLOBALS['APP']->redirect('http://dev.youxuanji.com/');
      return;
    }
    $GLOBALS['APP']->redirect(
      'http://dev.youxuanji.com/category-'.$category['parent_id'].'/'
    );
  }

  private function disableCategory($category) {
    $categoryId = $category['id'];
    if ($category['brand_amount'] === '0') {
      Db::update('category', array('is_active' => 0), 'id = ?', $categoryId);
    }
    $children = Db::getAll('SELECT * FROM category WHERE parent_id = ?', $categoryId);
    foreach ($children as $child) {
      $this->disableCategory($child);
    }
  }
}