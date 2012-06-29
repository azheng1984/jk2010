<?php
abstract class PublisherScreen extends ConsoleScreen {
  protected function renderConsoleContent() {
    echo '<a href="/"><h1>货比万家 - 广告发布商</h1></a><div id="toolbar">';
    echo '<span>root | </span>';
    echo '<a href="/sign_out">退出</a>';
    echo '</div>';
    echo '<ul id="navigator">';
    if ($_SERVER['REQUEST_URI'] === '/') {
      echo '<li><span>首页</span></li>';
    } else {
      echo '<li><a href="/">首页</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/active_order') {
      echo '<li><span>活跃订单</span></li>';
    } else {
      echo '<li><a href="/active_order">活跃订单</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/report') {
      echo '<li><span>效果报表</span></li>';
    } else {
      echo '<li><a href="/report">效果报告</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/payment') {
      echo '<li><span>结算</span></li>';
    } else {
      echo '<li><a href="/payment">结算</a></li>';
    }
      if ($_SERVER['REQUEST_URI'] === '/widget_maker') {
      echo '<li><span>推广控件</span></li>';
    } else {
      echo '<li><a href="/widget_maker">推广控件</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/account') {
      echo '<li><span>帐户设置</span></li>';
    } else {
      echo '<li><a href="/account">帐户设置</a></li>';
    }
    echo '</ul>';
    $this->renderMerchantContent();
  }

  abstract protected function renderMerchantContent();
}