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
    echo '<a href="/">首页</a>';
    $this->renderParents();
    echo ' &gt; '.$this->current['name'];
    echo '</div>';
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
      echo ' &gt; <a href="'.$this->getPath($distance).'">'
        .$category['name'].'</a>';
      --$distance;
    }
  }

  private function renderFullLink() {
    $path = '/';
    foreach ($this->parents as $category) {
      $path .= urlencode($category['name']).'/';
      echo ' &gt; <a href="'.$path.'">'.$category['name'].'</a>';
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