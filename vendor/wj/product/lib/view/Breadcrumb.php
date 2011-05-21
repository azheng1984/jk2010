<?php
class Breadcrumb {
  public function render() {
    echo '<a href="/">首页</a> ';
    /*
    foreach ($_GET['category'] as $cateory) {
      echo ' &gt; <b>'.$cateory['name'].'</b> ';
    }
    */
  }
}