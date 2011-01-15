<?php
class NotFoundScreen {
  public function render() {
    header('Content-Type: text/plain');
    echo 'in not found screen...';
  }
}
