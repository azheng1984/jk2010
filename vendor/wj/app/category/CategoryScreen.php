<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    echo '<a href="/">首页</a> ';
    foreach ($_GET['category'] as $cateory) {
      echo ' &gt; <b>'.$cateory['name'].'</b> ';
    }
    echo '[<a href="edit">编辑</a>]';
    echo '<br />';
    $isLeaf = true;
    $last = end($_GET['category']);
    foreach (Category::getList($last['id']) as $row) {
      $isLeaf = false;
      echo '<a href="'.urlencode($row['name']).'/">'.$row['name'].'</a> ';
    }
  }
}