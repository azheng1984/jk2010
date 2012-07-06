<?php
abstract class ConsoleScreen extends Screen {
  public function __construct() {
    if (isset($_COOKIE['user_session_id']) === false) {
      //redirect to login window
      header('HTTP/1.1 301 Moved Permanently');
      header('Location: /sign_in');
      $this->stop();
    }
  }

  protected function renderHtmlHeadContent() {
    $this->addCssLink('common');
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="header"><a href="/" id="logo"></a></div>';
    $this->renderConsoleContent();
  }

  abstract protected function renderConsoleContent();
}