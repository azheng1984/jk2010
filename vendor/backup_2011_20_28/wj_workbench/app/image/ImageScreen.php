<?php
class ImageScreen {
  public function render() {
    header('Content-type: image/jpeg');
    readfile('/home/wz/wj_img/2'.$_SERVER['REQUEST_URI']); //security issue
  }
}