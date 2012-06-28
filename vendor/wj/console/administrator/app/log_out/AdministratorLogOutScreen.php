<?php
class AdministratorLogOutScreen extends Screen {
  public function __construct() {
    setcookie('user_session_id', '');
  }

  protected function renderHtmlHeadContent() {
    // TODO Auto-generated method stub
  }

  protected function renderHtmlBodyContent() {
    echo 'log out <a href="/sign_in">sign in</a>';
  }
}