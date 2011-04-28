<?php
class CategoryScreen extends Screen {
  public function renderContent() {
    echo '<a href="/">首页</a> ';
    foreach ($_GET['category'] as $cateory) {
      echo ' &gt; <b>'.$cateory['name'].'</b> ';
    }
    $db = new PDO(
      "mysql:host=localhost;dbname=wj",
      'root',
      'a841107!',
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
    echo '<br />';
    $isLeaf = true;
    $last = end($_GET['category']);
    foreach ($db->query('select * from global_category where parent_id='.$last['id']) as $row) {
      $isLeaf = false;
      echo '<a href="'.urlencode($row['name']).'/">'.$row['name'].'</a> ';
    }
  }
}