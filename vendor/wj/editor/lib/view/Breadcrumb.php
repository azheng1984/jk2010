<?php
class Breadcrumb {
  public function render() {
    echo '<a href="/">首页</a> ';
    $category = Category::getById($_GET['id']);
    $categories = $category->getParentLinkList();
    print_r($categories);
    $distance = count($categories) - 1;
    foreach ($categories as $name => $item) {
      if ($name == $category->getName()) {
        echo ' &gt; <b>'.$name.'</b> ';
      } else {
        echo ' &gt; <a href="'.$this->getPath($distance).'">'.$name.'</a> ';
      }
      --$distance;
    }
    /*
    foreach ($_GET['category'] as $cateory) {
      echo ' &gt; <b>'.$cateory['name'].'</b> ';
    }
    */
  }

  private function getPath($distance) {
    $path = '';
    while ($distance > 0) {
      if ($path !== '') {
        $path .= '/';
      }
      $path .= '..';
      --$distance;
    }
    return $path;
  }
}