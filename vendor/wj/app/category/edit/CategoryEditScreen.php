<?php
class CategoryEditScreen extends Screen {
  public function renderContent() {
    echo '<a href="/">首页</a> ';
    foreach ($_GET['category'] as $cateory) {
      echo ' &gt; <b>'.$cateory['name'].'</b> ';
    }
    echo '<br />';
    $isLeaf = true;
    $last = end($_GET['category']);
    foreach (Db::execute('select * from global_category where parent_id='.$last['id']) as $row) {
      $isLeaf = false;
      echo '<a href="'.urlencode($row['name']).'/">'.$row['name'].'</a> [<a href="'.urlencode($row['name']).'.edit/">编辑</a>]';
    }
  }
}