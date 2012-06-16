<?php
class SignUpScreen {
  public function render() {
    header('Content-Type:text/html; charset=UTF-8');
    echo '<form method="POST"><div>用户名<input type="text"/></div>';
    echo '<div>密码<input type="password"/></div><div><input type="submit"/></div></form>';
  }
}