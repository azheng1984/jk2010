<?php
class MerchantIOCallbackStatusScreen extends MerchantScreen {
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
    echo '<div id="toolbar">用户名 | publisher_id：xxx | <a href="sign_out">退出</a></div>';
    MerchantNavigationScreen::render('home');
    echo '<h2>数据接口</h2>';
    echo '<a href="/io">数据接口</a> / 订单回调状态';
    echo '<h3>状态</h3>';
    echo 'OK';
    echo '<h3>故障历史</h3>';
    echo '时间 | 原因';
    echo '<h3 title="故障数量/请求数量">可用率</h3>';
    echo '今天 | 昨天 | 最近 7 天 | 最近 30 天 | 最近 90 天 | 最近 365 天';
    echo '<h3 title="单位：天">吞吐率</h3>';
    echo '今天 | 昨天 | 最近 7 天 | 最近 30 天 | 最近 90 天 | 最近 365 天';
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