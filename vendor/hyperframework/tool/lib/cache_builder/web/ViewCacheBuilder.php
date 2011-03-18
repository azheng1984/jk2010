<?php
class ViewCacheBuilder {
  public function build($fileName) {
    $suffix = substr($fileName, -10);
    $cache = array();
    if ($suffix === 'Screen.php') {
      $cache['screen'] = preg_replace('/.php$/', '', $fileName);
    }
    if (substr($fileName, -9) === 'Image.php') {
      $cache['image'] = preg_replace('/.php$/', '', $fileName);
    }
    if (count($cache) === 0) {
      return;
    }
    return $cache;
  }
}