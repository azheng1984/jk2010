<?php
class BrandNewScreen {
  public function __construct() {
    
  }

  public function render() {
    echo '添加品牌';
    echo '<form action="." method="POST">';
    echo '名称：<input name="name" />';
    $parentId = 0;
    if (isset($_GET['parent_id'])) {
      $parentId = intval($_GET['parent_id']);
    }
    echo '父分类 id：<input name="parent_id" value="'.$parentId.'" />';
    echo '<input type="submit" value="提交" />';
    echo '</form>';
  }
}