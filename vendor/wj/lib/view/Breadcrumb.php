<?php
class Breadcrumb {
  public function render($categories, $product = null) {
    $current = $product;
    if ($product === null) {
      $current = array_pop($categories);
    }
    echo '<div id="breadcrumb">';
    echo '<a href="/">首页</a> ';
    if ($product === null) {
      $this->renderRelativeLink($categories);
      echo '&gt; <h1>'.$current['name'].'</h1>';
    } else {
      $this->renderFullLink($categories);
      echo '&gt; '.$current['name'];
    }
    echo '</div>';
  }

  private function renderRelativeLink($categories) {
    $distance = count($categories);
    foreach ($categories as $category) {
      echo '&gt; <a href="'.$this->getPath($distance).'">'
        .$category['name'].'</a> ';
      --$distance;
    }
  }

  private function renderFullLink($categories) {
    $path = '/';
    foreach ($categories as $category) {
      $path .= urlencode($category['name']).'/';
      echo '&gt; <a href="'.$path.'">'.$category['name'].'</a> ';
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