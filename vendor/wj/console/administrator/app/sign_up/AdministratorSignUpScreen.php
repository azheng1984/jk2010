<?php
class AdministratorSignUpScreen extends Screen {
/* (non-PHPdoc)
 * @see Screen::renderHtmlHeadContent()
 */protected function renderHtmlHeadContent() {
    // TODO Auto-generated method stub
  }

/* (non-PHPdoc)
 * @see Screen::renderHtmlBodyContent()
 */protected function renderHtmlBodyContent() {
    echo '管理员注册';
    echo '<form method="POST"><div>用户名<input type="text"/></div>';
    echo '<div>密码<input type="password"/></div><div><input type="submit"/></div></form>';
  }
}