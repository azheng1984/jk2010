<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo '[<a href="/category/edit?id=">编辑</a> | <a href="category/new?parent_id=">新建分类</a>]';
    echo '<br />';
    $isLeaf = true;
    //Category::getById();
    //$last = end($_GET['category']);
    foreach (Category::getList(Category::getById($_GET['id'])) as $row) {
      if (!$row['table_prefix']) {
        echo '<div><a href="/category?id='.$row['id'].'">'.$row['name'].'</a><input type="button" value="删除" /></div>';
      } else {
        echo '<div><a href="/product_list?category_id='.$row['id'].'">'.$row['name'].'</a><input type="button" value="删除" /></div>';
      }
    }
  }
}