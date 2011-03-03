<?php
class NotFoundImage {
  public function render() {
    header('Content-Type: image/png');
    readfile(ROOT_PATH.'public/image/error.png');
  }
}