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
      '用户名：<input name="username" type="text"/>密码：<input name="password" type="password"/>验证码：（同一帐号，3次错误时显示）<input name="verification_code" type="text"/><input value="登录" type="submit"/></form>';
    echo '<a href="/sign_up">注册</a>';
  }
}