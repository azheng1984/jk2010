<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo '[<a href="?view=edit">编辑</a> | <a href="?view=new_category">新建分类</a>]';
    echo '<br />';
    $isLeaf = true;
    $last = end($_GET['category']);
    foreach (Category::getList($last['id']) as $row) {
      $isLeaf = false;
      echo '<div><a href="'.urlencode($row['name']).'/">'.$row['name'].'</a><input type="button" value="删除" /></div>';
    }
  }
}