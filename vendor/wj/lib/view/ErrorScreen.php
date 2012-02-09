<?php
abstract class ErrorScreen extends Screen {
  abstract protected function getMessage();
  abstract protected function getCode();

  protected function renderHtmlHeadContent() {
  }

  protected function renderHtmlBodyContent() {
  }
}