<?php
class AdministratorScreen extends Screen {
	/* (non-PHPdoc)
 * @see Screen::renderHtmlHeadContent()
 */protected function renderHtmlHeadContent() {
    // TODO Auto-generated method stub
  }

	/* (non-PHPdoc)
 * @see Screen::renderHtmlBodyContent()
 */protected function renderHtmlBodyContent() {
    echo '账户管理';
    echo '<form method="POST"><div>原密码<input type="text"/></div>';
    echo '<div>新密码<input type="password"/></div><div><input type="submit"/></div></form>';
  }
}