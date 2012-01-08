<?php
class ImageScreen {
  public function render() {
    //add & check etag
    $tmps = explode('/', $_SERVER['REQUEST_URI'], 2);
    $filename = $tmps[1];
    $tmps = explode('.', $filename, 2);
    $id = $tmps[0];
    header('Content-type: image/jpeg');
    echo DbImage::get($id);
  }
}