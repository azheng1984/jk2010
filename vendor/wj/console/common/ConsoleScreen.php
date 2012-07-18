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
    echo '<title>', $this->getTitle(), '-货比万家</title>';
    $this->addCssLink('common');
    $this->addJsLink('common');
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="header"><a href="/" id="logo"> </a>';
    echo '<a href="/"><h1>',$this->getRole(),'</h1></a>';
    $this->renderNav();
    echo '<div id="toolbar">';
    echo '<span>root </span>';
    echo '<a id="sign_out" href="/sign_out">退出</a>';
    echo '</div></div><div id="console"><div class="content">';
    $this->renderConsoleContent();
    echo '</div></div><div id="footer">© 2012 货比万家</div>';
  }

  abstract protected function getRole();

  abstract protected function getTitle();

  abstract protected function renderConsoleContent();
}