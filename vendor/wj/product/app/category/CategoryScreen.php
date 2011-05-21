<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    $category = $GLOBALS['category'];
    echo '[<a href="'.$category->getEditLink().'">编辑</a> | <a href="'.$category->getNewLink().'">新建分类</a>]';
    echo '<br />';
    foreach (Category::getList($GLOBALS['category']) as $row) {
      echo '<div><a href="'.urlencode($row['name']).'/">'.$row['name'].'</a><input type="button" value="删除" /></div>';
    }
  }
}