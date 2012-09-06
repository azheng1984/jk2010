<?php
class PublisherIOScreen extends PublisherScreen {
  public function __construct() {
  }

  protected function renderHtmlHeadContent() {
    echo '<title>广告发布商 / 数据接口 - 货比万家</title>';
  }

  protected function renderHtmlBodyContent() {
    if (isset($_COOKIE['session_id']) === false) {
      $this->renderSignIn();
      return;
    }
    echo '<h1><a href="/">广告发布商</a></h1>';
    echo '<div id="toolbar">用户名 | <a href="sign_out">退出</a></div>';
    PublisherNavigationScreen::render('home');
    echo '<h2>数据接口</h2>';
    echo '<h3>API</h3>';
    echo '<ul>';
    echo '<li><a href="io/performance_report">效果报告</a></li>';
    echo '<li><a href="io/payment">结算</a></li>';
    echo '</ul>';
    echo '<h3>回调</h3>';
    echo '<ul>';
    echo '<li><a href="io/order">订单</a></li>';
    echo '<li><a href="io/status">状态：OK</a> (最后回调时间: 2012-11-1)</li>';
    echo '<li><a href="io/setting">设置</a></li>';
    echo '</ul>';
    $this->renderFooter();
  }

  private function renderSignIn() {
    echo '<form method="POST" action="/">';
    echo '<div><label for="username">用户名：</label><input id="username" name="username" type="text" /></div>';
    echo '<div><label for="password">密码：</label><input id="password" name="password" type="password" /></div>';
    echo '<div><input type="submit" value="登录" />';
    echo '<a href="/sign_up">注册</a></div>';
    echo '</form>';
  }

  private function renderFooter() {
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/" target="_blank">货比万家</a></div>';
  }
}