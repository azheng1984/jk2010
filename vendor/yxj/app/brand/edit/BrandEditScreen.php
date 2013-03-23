<?php
class BrandEditScreen {
  public function __construct() {
    
  }

  public function render() {
    echo '添加品牌';
    echo '<form action="." method="POST" enctype="multipart/form-data">';
    echo '<div>名称：<input name="name" /></div>';
    echo '<div>图标：<input name="logo" type="file" /></div>';
    echo '<div>等级：<input id="r3" name="rank" type="radio" value ="3" /><label for="r3">顶级</label>';
    echo '<div><input id="r2" name="rank" type="radio" value ="2" /><label for="r2">好</label>';
    echo '<div><input id="r1" name="rank" type="radio" value ="1" /><label for="r1">一般</label>';
    echo '</div>';
    echo '<div>发源地 id：<input name="location_id" /></div>';
    echo '<div>父品牌 id：<input name="parent_id" /></div>';
    echo '<div>分类：<textarea name="category_id_list">';
    if (isset($_GET['category_id'])) {
      echo $_GET['category_id'];
    }
    echo '</textarea></div>';
    echo '<div>摘要：<input name="abstract" /></div>';
    echo '<div>内容：<textarea name="content"></textarea></div>';
    $parentId = 0;
    echo '<input type="submit" value="提交" />';
    echo '</form>';
  }
}