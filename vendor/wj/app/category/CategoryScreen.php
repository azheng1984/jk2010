<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo '[<a href="edit">编辑</a> | <a href="new">新建分类</a>]';
    echo '<br />';
    $isLeaf = true;
    $last = end($_GET['category']);
    foreach (Category::getList($last['id']) as $row) {
      $isLeaf = false;
      echo '<a href="'.urlencode($row['name']).'/">'.$row['name'].'</a><input type="button" value="删除" />';
    }
  }
}