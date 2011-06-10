<?php
class Breadcrumb {
  public function render() {
    if (!isset($_GET['category_id']) && !isset($_GET['id'])) {
      return;
    }
    echo '<a href="/">首页</a> ';
    $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : $_GET['id'];
    $category = Category::getById($categoryId);
    $categories = $category->getParentLinkList();
    $distance = count($categories) - 1;
    foreach ($categories as $item) {
      $name = $item[0];
      if ($name == $category->getName() && !isset($_GET['category_id'])) {
        echo ' &gt; <b>'.$name.'</b> ';
      } else {
        echo ' &gt; <a href="/category?id='.$item[1].'">'.$name.'</a> ';
      }
      --$distance;
    }
  }
}