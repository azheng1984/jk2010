<?php
class PublisherIOStatusScreen extends PublisherScreen {
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
    echo '<a href="/io">数据接口</a> / <a href="/io/order">订单</a> / 状态';
    echo '<h3>历史记录</h3>';
    echo '开始时间 | 结束时间 | 执行持续时间 | 订单数量 | 状态';
    echo '<h3>统计</h3>';
    echo '可用率：今天 | 1个月 | 3个月 | 1年';
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