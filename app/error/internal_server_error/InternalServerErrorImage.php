<?php
class InternalServerErrorImage {
  public function render() {
    header('Content-Type: image/jpeg');
    readfile(ROOT_PATH.'public/image/error.jpg');
  }
}