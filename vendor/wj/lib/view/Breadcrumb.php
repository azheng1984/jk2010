<?php
class Breadcrumb {
  public function render($categories, $product = null) {
    $current = $product;
    $parents = $categories;
    if ($current === null) {
      $current = array_pop($parents);
    }
    echo '<div id="breadcrumb">';
    echo '<a href="/">首页</a>';
    $this->renderParents($parents, $product === null);
    echo ' &gt; '.$current['name'];
    echo '</div>';
  }

  private function renderParents($parents, $isRelative) {
    if ($isRelative) {
      $this->renderRelativeLink($parents);
      return;
    }
    $this->renderFullLink($parents);
  }

  private function renderRelativeLink($parents) {
    $distance = count($parents);
    foreach ($parents as $category) {
      echo ' &gt; <a href="'.$this->getPath($distance).'">'
        .$category['name'].'</a>';
      --$distance;
    }
  }

  private function renderFullLink($parents) {
    $path = '/';
    foreach ($parents as $category) {
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