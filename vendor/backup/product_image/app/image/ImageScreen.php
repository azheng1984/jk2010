<?php
class ImageScreen {
  public function render() {
    $tmps = explode('/', $_SERVER['REQUEST_URI'], 2);
    $filename = $tmps[1];
    $tmps = explode('.', $filename, 2);
    $id = $tmps[0];
    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
      header('HTTP/1.1 304 Not Modified');
      return;
    }
    header('Content-type: image/jpeg');
    header('Etag: 0');
    echo DbImage::get($id);
  }
}