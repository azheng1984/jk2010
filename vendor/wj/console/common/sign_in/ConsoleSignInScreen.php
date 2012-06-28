<?php
class ConsoleSignInScreen extends Screen {
  public function __construct() {
    setcookie('user_session_id', '123');
  }

  protected function renderHtmlHeadContent() {
    // TODO Auto-generated method stub
  }

  protected function renderHtmlBodyContent() {
    echo '<form action="/" method="POST">Sign in ',
      '<input name="username" type="text"/><input name="password" type="password"/><input value="登录" type="submit"/></form>';
    echo '<a href="/sign_up">注册</a>';
  }
}