<?php
class PublisherHomeScreen extends PublisherScreen {
  public function __construct() {
//     if (isset($_COOKIE['session_id']) === false) {
//       header('HTTP/1.1 302 Found');
//       header('Location: /sign_in');
//       $this->stop();
//     }
  }

  protected function renderHtmlHeadContent() {
    echo '';
  }

  protected function renderHtmlBodyContent() {
    if (isset($_COOKIE['session_id']) === false) {
      $this->renderSignIn();
      return;
    }
    echo '<h1><a href="/">广告发布商</a></h1>';
    echo '<div id="toolbar">用户名 | <a href="sign_out">退出</a></div>';
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/">货比万家</a></div>';
  }

  private function renderSignIn() {
    echo '<form method="POST" action="/sign_in">';
    echo '<div><label for="username">用户名：</label><input id="username" name="username" type="text" /></div>';
    echo '<div><label for="password">密码：</label><input id="password" name="password" type="password" /></div>';
    echo '<div><input type="submit" value="登录" />';
    echo '<a href="/sign_up">注册</a></div>';
    echo '</form>';
  }
}