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
    echo '<div id="breadcrumb">',
      '<a href="/" rel="nofollow">首页</a> ';
    $this->renderParents();
    echo $this->renderArrow(),
      '<strong>'.$this->current['name'],'</strong></div>';
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
      $this->renderParentLink($this->getRelativePath($distance), $category['name']);
      --$distance;
    }
  }

  private function renderFullLink() {
    $path = '/';
    $leaf = end($this->parents);
    foreach ($this->parents as $category) {
      $path .= urlencode($category['name']).'/';
      $this->renderParentLink($path, $category['name']);
    }
  }

  private function renderParentLink($path, $name) {
    echo $this->renderArrow().'<a href="'.$path.'"> '.$name.'</a> ';
  }

  private function renderArrow() {
    echo '<span class="arrow">&rsaquo;</span> ';
  }

  private function getRelativePath($distance) {
    for ($path = ''; $distance > 0; --$distance) {
      if ($path !== '') {
        $path .= '/';
      }
      $path .= '..';
    }
    return $path;
  }
}