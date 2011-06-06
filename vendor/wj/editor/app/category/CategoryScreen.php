<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    //$breadcrumb = new Breadcrumb;
    //$breadcrumb->render();
    echo '[<a href="/category/edit?id=">编辑</a> | <a href="category/new?parent_id=">新建分类</a>]';
    echo '<br />';
    $isLeaf = true;
    //Category::getById();
    //$last = end($_GET['category']);
    foreach (Category::getList($_GET['id']) as $row) {
      $isLeaf = false;
      echo '<div><a href="/category?id='.$row['id'].'">'.$row['name'].'</a><input type="button" value="删除" /></div>';
    }
  }
}