<?php
class PublisherUnfinishedOrderScreen extends PublisherScreen {
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
    $this->renderDashboard();
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

  private function renderDashboard() {
    echo '<h2>概览</h2>';
    echo '<h3>未付款</h3>';
    echo '<ul>';
    echo '<li>账户余额：</li>';
    echo '<li><a href="/payment/unpaid">明细</a></li>';
    echo '<li><a href="/payment/pay_cash">付款</a></li>';
    echo '</ul>';
    echo '<h3>正在付款</h3>';
    echo '<ul>';
    echo '<li>付款总额：</li>';
    echo '<li><a href="/payment/processing">明细</a></li>';
    echo '</ul>';
    echo '<h3>未完成订单佣金</h3>';
    echo '<ul>';
    echo '<li>佣金总额：</li>';
    echo '<li><a href="/payment/unfinished_order">明细</a></li>';
    echo '</ul>';
  }

  private function renderFooter() {
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/" target="_blank">货比万家</a></div>';
  }
}