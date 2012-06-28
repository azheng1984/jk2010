<?php
class AdministratorSignOutScreen extends Screen {
  public function __construct() {
    setcookie('user_session_id', '');
    $this->stop();
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: /sign_in');
  }

  protected function renderHtmlHeadContent() {}

  protected function renderHtmlBodyContent() {}
}