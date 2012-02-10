<?php
abstract class ErrorScreen extends Screen {
  public function __construct() {
    header('Cache-Control: private, max-age=0');
  }

  abstract protected function getMessage();
  abstract protected function getCode();

  protected function renderHtmlHeadContent() {
  }

  protected function renderHtmlBodyContent() {
  }
}