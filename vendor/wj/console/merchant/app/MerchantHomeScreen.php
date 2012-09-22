<?php
class MerchantHomeScreen extends MerchantScreen {
  public function __construct() {
  }

  protected function renderHtmlHeadContent() {
    echo '<title>商家 - 货比万家</title>';
  }

  protected function renderHtmlBodyContent() {
    if (isset($_COOKIE['session_id']) === false) {
      $this->renderSignIn();
      return;
    }
    echo '<h1><a href="/">商家</a></h1>';
    echo '<div id="toolbar">用户名 | <a href="sign_out">退出</a></div>';
    MerchantNavigationScreen::render('home');
    $this->renderToday();
    $this->renderYesterday();
    $this->renderTotal();
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

  private function renderToday() {
    echo '<h2>今天</h2>';
    echo '<ul>';
    echo '<li>流量：</li>';
    echo '<li>预计订单数量：当天流量 | 全部流量</li>';
    echo '<li>预计订单交易金额：当天流量 | 全部流量</li>';
    echo '<li>预计订单佣金：当天流量 | 全部流量</li>';
    echo '<li title="当天流量的预计订单佣金 / 流量">预计 CPC：</li>';
    echo '</ul>';
  }

  private function renderYesterday() {
    echo '<h2>昨天</h2>';
    echo '<ul>';
    echo '<li>流量：</li>';
    echo '<li>预计订单数量：当天流量 | 全部流量</li>';
    echo '<li>预计订单交易金额：当天流量 | 全部流量</li>';
    echo '<li>预计订单佣金：当天流量 | 全部流量</li>';
    echo '<li title="当天流量的预计订单佣金 / 流量">预计 CPC：</li>';
    echo '</ul>';
  }

  private function renderTotal() {
    echo '<h2>总计</h2>';
    echo '<ul>';
    echo '<li>账户余额：</li>';
    echo '<li>正在付款：</li>';//optional
    echo '<li>未完成订单佣金：</li>';
    echo '</ul>';
  }

  private function renderFooter() {
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/" target="_blank">货比万家</a></div>';
  }
}