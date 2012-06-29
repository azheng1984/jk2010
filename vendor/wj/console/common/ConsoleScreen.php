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
    // TODO Auto-generated method stub
  }

  protected function renderHtmlBodyContent() {
    echo '<a href="/"><h1>货比万家 - 管理员</h1></a><div id="toolbar">';
    echo '<span>root | </span>';
    echo '<a href="/sign_out">退出</a>';
    echo '</div>';

    $this->renderConsoleContent();
  }

  abstract protected function renderConsoleContent();
}