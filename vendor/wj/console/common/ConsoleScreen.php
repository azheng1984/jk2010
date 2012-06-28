<?php
class ConsoleScreen extends Screen {
  public function __construct() {
    if (isset($_COOKIE['user_session_id']) === false) {
      //redirect to login window
      header('HTTP/1.1 301 Moved Permanently');
      header('Location: /sign_in');
      $this->stop();
    }
  }

  protected function renderHtmlHeadContent() {
    // TODO Auto-generated method stub
  }

  protected function renderHtmlBodyContent() {
    echo '<h1>管理员</h1><div id="toolbar">';
    echo '<span>yywz@126.com | </span>';
    echo '<a href="/sign_out">退出</a>';
    echo '</div>';
    echo '<ul>',
      '<li><b style="background:red;color:#fff;padding:2px 5px">首页</b></li>',
      '<li><a href="/publisher">广告发布商</a></li>',
      '<li><a href="/merchant">商家<a></li>',
      '<li><a href="/administrator">管理员</a>（只有 root 用户才有）</li>',
      '<li><a href="/notification">通知</a></li>',
      '<li><a href="/account">帐号设置</a></li>';
    echo '</ul>';
  }
}