<?php
class PublisherPaymentHistoryScreen extends PublisherScreen {
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
    $this->renderBreadcrumb();
    if (isset($_GET['id'])) {
      $this->renderMerchant();
    } elseif (isset($_GET['merchant_id'])) {
      $this->renderOrder();
    } elseif (isset($_GET['order_id'])) {
      $this->renderOrderDetail();
    } else {
      $this->renderHistory();
    }
    $this->renderFooter();
  }

  private function renderBreadcrumb() {
    echo '<h2>结算</h2>';
    echo '<a href="/payment">概览</a>';
    if (isset($_GET['id'])) {
      echo ' / <a href="history">付款历史</a> / <strong>支付编号:1 (2012-1-1)</strong>';
    } elseif (isset($_GET['merchant_id'])) {
      echo ' / <a href="history">付款历史</a> / <a href="?id=1">支付编号:1 (2012-1-1)</a> / <strong>商家</strong>';
    } elseif (isset($_GET['order_id'])) {
      echo ' / <a href="history">付款历史</a> / <a href="?id=1">支付编号:1 (2012-1-1)</a> / <a href="?merchant_id=1">商家</a> / <strong>订单</strong>';
    } else {
      echo ' / <strong>付款历史</strong>';
    }
  }

  private function renderHistory() {
    echo '<div>支付编号 | 日期 | 订单数量 | 交易金额 | 佣金</div>';
    echo '<a href="?id=1">next level</a>';
    echo '<div>总计：订单数量 交易金额 佣金</div>';
  }

  private function renderMerchant() {
    echo '<div>商家 | 订单数量 | 交易金额 | 佣金</div>';
    echo '<a href="?merchant_id=1">next level</a>';
    echo '<div>总计：订单数量 交易金额 佣金</div>';
  }

  private function renderOrder() {
    echo '<div>时间 | 渠道 | 跟踪编号 | 商家 | 订单编号 | 交易金额 | 佣金</div>';
    echo '<a href="?order_id=1">next level</a>';
    echo '<div>总计：订单数量 交易金额 佣金</div>';
  }

  private function renderOrderDetail() {
    echo '<div>商品编号 | 商品名称 | 单价 | 数量 | 交易金额 | 佣金</div>';
    echo '<div>总计：交易金额 佣金</div>';
  }

  private function renderFooter() {
    echo '<div id="footer">© 2012 <a href="http://dev.huobiwanjia.com/" target="_blank">货比万家</a></div>';
  }
}