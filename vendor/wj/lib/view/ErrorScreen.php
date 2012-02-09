<?php
abstract class ErrorScreen extends Screen {
  abstract protected function renderMessage();

  protected function renderHtmlHeadContent() {
  }

  protected function renderHtmlBodyContent() {
  }
}