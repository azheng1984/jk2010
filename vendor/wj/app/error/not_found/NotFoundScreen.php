<?php
class NotFoundScreen extends ErrorScreen {
  protected function getMessage() {
    echo '页面没找到(404 Not Found)';
  }

  protected function getCode() {
    return 404;
  }
}