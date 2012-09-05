<?php
class PublisherPaymentSettingScreen extends PublisherScreen {
  public function __construct() {
    if (isset($_COOKIE['session_id']) === false) {
      header('HTTP/1.1 302 Found');
      header('Location: /');
      $this->stop();
    }
  }

  protected function renderHtmlHeadContent() {
    echo '<title>广告发布商 / 结算 - 货比万家</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<h1><a href="/">广告发布商</a></h1>';
    echo '<div id="toolbar">用户名 | <a href="sign_out">退出</a></div>';
    PublisherNavigationScreen::render('payment');
    $this->renderMenu();
    $this->renderSetting();
    $this->renderFooter();
  }

  private function renderMenu() {
    echo '<h2>二级菜单</h2>';
    echo '<ul>';
    echo '<li><a href="/payment">概览</li>';
    echo '<li><a href="/payment/unpaid">未付款</li>';
    echo '<li><a href="/payment/processing">正在付款</li>';
    echo '<li><a href="/payment/unfinished_order">未完成订单佣金</li>';
    echo '<li><a href="/payment/history">付款历史</li>';
    echo '<li><a href="/payment/setting">设置</a></li>';
    echo '</ul>';
  }

  private function renderSetting() {
    echo '<h2>收款账户</h2>';
    echo '<div>开户行 | 户名 | 帐号</div>';
    echo '<h2>自动付款</h2>';
    echo '<div>开启 | 关闭</div>';
    echo '<h2>代扣税费</h2>';
    echo '<div>开启 | 关闭</div>';
  }

  private function renderFooter() {
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/" target="_blank">货比万家</a></div>';
  }
}