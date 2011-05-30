<?php
class Breadcrumb {
  public function render() {
    echo '<a href="/">首页</a> ';
    $names = $GLOBALS['category']->getParentLinkList();
    $currentLink = array_pop($names);
    foreach ($names as $name => $link) {
      echo ' &gt; <a href="'.$link.'">'.$name.'</a> ';
    }
    if (isset($GLOBALS['product'])) {
      echo ' &gt; <a href="'.$currentLink.'">'.$GLOBALS['category']->getName().'</a> ';
      echo ' &gt; <b>'.$GLOBALS['product']->getTitle().'</b>';
    } else {
      echo ' &gt; <b>'.$GLOBALS['category']->getName().'</b>';
    }
  }
}