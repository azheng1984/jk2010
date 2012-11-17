<?php
class PublisherPaymentScreen extends PublisherScreen {
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
    echo '<div id="toolbar">用户名 | publisher_id：xxx | <a href="sign_out">退出</a></div>';
    PublisherNavigationScreen::render('payment');
    $this->renderDashboard();
    $this->renderFooter();
  }

  private function renderDashboard() {
    echo '<h2>结算</h2>';
    //  echo '<div>概览</div>';
    echo '<h3><a href="/payment/unpaid">未付款</a></h3>';
    echo '<ul>';
    echo '<li>账户余额：</li>';
    echo '<li><a href="/payment/pay_cash">付款</a></li>';
    echo '</ul>';
    echo '<h3><a href="/payment/processing">正在付款</a></h3>';
    echo '<ul>';
    echo '<li>付款总额：</li>';
    echo '</ul>';
    echo '<h3><a href="/payment/history">付款历史</a></h3>';
    echo '<h3><a href="/payment/setting">设置</a></h3>';
    echo '<ul>';
    echo '<li>收款账户：</li>';
    echo '<li>自动付款：</li>';
    echo '<li>代扣税费：</li>';
    echo '</ul>';
  }

  private function renderFooter() {
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/" target="_blank">货比万家</a></div>';
  }
}