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
    $this->renderBreadcrumb();
    if (isset($_GET['merchant_id'])) {
      $this->renderOrder();
    } elseif (isset($_GET['order_id'])) {
      $this->renderOrderDetail();
    } else {
      $this->renderMerchant();
    }
    $this->renderFooter();
  }

  private function renderBreadcrumb() {
    if (isset($_GET['merchant_id'])) {
      echo '<a href="unfinished_order">未完成订单佣金</a> / 商家';
    } elseif (isset($_GET['order_id'])) {
      echo '<a href="unfinished_order">未完成订单佣金</a> / <a href="?merchant_id=1">商家</a> / 订单';
    }
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