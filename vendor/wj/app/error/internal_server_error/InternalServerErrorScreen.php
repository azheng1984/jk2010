<?php
class InternalServerErrorScreen extends ErrorScreen {
  protected function getMessage() {
    return '出错了(500 Internal Server Error)';
  }

  protected function getCode() {
    return 500;
  }
}