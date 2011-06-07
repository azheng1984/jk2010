<?php
class Breadcrumb {
  public function render() {
    echo '<a href="/">首页</a> ';
    $names = $GLOBALS['category']->getParentLinkList();
    $currentLink = array_pop($names);
    $distance = count($names);
    foreach ($names as $name => $link) {
      echo ' &gt; <a href="'.$this->getPath($distance).'">'.$name.'</a> ';
      --$distance;
    }
    if (isset($GLOBALS['product'])) {
      echo ' &gt; <a href=".">'.$GLOBALS['category']->getName().'</a> ';
      echo ' &gt; <b>'.$GLOBALS['product']->getTitle().'</b>';
    } else {
      echo ' &gt; <b>'.$GLOBALS['category']->getName().'</b>';
    }
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