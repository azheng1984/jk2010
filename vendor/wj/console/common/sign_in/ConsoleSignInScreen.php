<?php
class ConsoleSignInScreen {
  public function render() {
    echo '<meta charset="UTF-8"/>';
    echo '<form action="/" method="POST">Sign in <input name="username" type="text"/><input name="password" type="password"/><input value="登录" type="submit"/></form>';
    echo '<a href="/sign_up">注册</a>';
  }
}