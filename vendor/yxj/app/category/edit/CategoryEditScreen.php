<?php
class CategoryEditScreen {
  public function __construct() {
    
  }

  public function render() {
    if (!is_array($GLOBALS['PATH_SECTION_LIST'][1])) {
      exit;
    }
    $categoryId = $GLOBALS['PATH_SECTION_LIST'][1][1];
    $category = Db::getRow('SELECT * FROM category WHERE id = ?', $categoryId);
    echo '<form action="." method="POST">';
    echo '<input type="hidden" name="_method" value="PUT" />';
    echo '名称：<input name="name" value="'.$category['name'].'"/>';
    echo '父分类 id：<input name="parent_id" value="'.$category['parent_id'].'" />';
    echo '<input type="submit" value="提交" />';
    echo '</form>';
  }
}