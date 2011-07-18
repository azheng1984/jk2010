<?php
class Breadcrumb {
  public function render($categories) {
    $current = array_pop($categories);
    echo '<div id="breadcrumb">';
    echo '<a href="/">首页</a> ';
    $distance = count($categories);
    foreach ($categories as $category) {
      echo '&gt; <a href="'.$this->getPath($distance).'">'
        .$category['name'].'</a> ';
      --$distance;
    }
    echo '&gt; '.$current['name'];
    echo '</div>';
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