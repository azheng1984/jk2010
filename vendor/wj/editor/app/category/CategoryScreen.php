<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    echo '[<a href="/category/edit?id='.$_GET['id'].'">编辑</a> | <a href="category/new?parent_id='.$_GET['id'].'">新建分类</a>]';
    echo '<br />';
    $isLeaf = true;
    //Category::getById();
    //$last = end($_GET['category']);
    foreach (Category::getList(Category::getById($_GET['id'])) as $row) {
      if (!$row['table_prefix']) {
        echo '<div><a href="/category?id='.$row['id'].'">'.$row['name'].'</a>';
        echo '<form method="POST"><input type="hidden" name="_method" value="DELETE" /><input type="hidden" name="id" value="'.$row['id'].'" /><input type="submit" value="删除" /></form></div>';
      } else {
        echo '<div><a href="/product_list?category_id='.$row['id'].'">'.$row['name'].'</a><form action="." method="POST"><input type="button" value="删除" /></form></div>';
      }
    }
  }
}