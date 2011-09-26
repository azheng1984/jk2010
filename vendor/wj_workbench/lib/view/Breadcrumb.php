<?php
class Breadcrumb {
  private $current;
  private $parents;
  private $isRelative;

  public function __construct($categories, $product = null) {
    $this->parents = $categories;
    $this->current = $product;
    if ($this->current === null) {
      $this->current = array_pop($this->parents);
    }
    $this->isRelative = $product === null;
  }

  public function render() {
    echo '<div id="breadcrumb">';
    echo '<a class="parent" href="/" rel="nofollow">首页</a>';
    $this->renderParents();
    echo ' <span class="arrow">&rsaquo;</span> <strong>'.$this->current['name'];
    echo '</strong></div>';
  }

  private function renderParents() {
    if ($this->isRelative) {
      $this->renderRelativeLink();
      return;
    }
    $this->renderFullLink();
  }

  private function renderRelativeLink() {
    $distance = count($this->parents);
    foreach ($this->parents as $category) {
      echo ' <span class="arrow">&rsaquo;</span> <a class="parent" href="'.$this->getPath($distance).'">'
        .$category['name'].'</a>';
      --$distance;
    }
  }

  private function renderFullLink() {
    $path = '/';
    $leaf = end($this->parents);
    foreach ($this->parents as $category) {
      $path .= urlencode($category['name']).'/';
      echo ' <span class="arrow">&rsaquo;</span> <a ';
      if ($leaf['id'] !== $category['id']) {
        echo 'class="parent" ';
      }
      echo 'href="'.$path.'"> '.$category['name'].'</a> ';
    }
  }

  private function getPath($distance) {
    for ($path = ''; $distance > 0; --$distance) {
      if ($path !== '') {
        $path .= '/';
      }
      $path .= '..';
    }
    return $path;
  }
}