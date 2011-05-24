<?php
class Breadcrumb {
  public function render() {
    echo '<a href="/">首页</a> ';
    $names = $GLOBALS['category']->getParentLinkList();
    foreach ($GLOBALS['category']->getParentLinkList() as $name => $link) {
      echo ' &gt; <a href="'.$link.'">'.$name.'</a> ';
    }
  }
}