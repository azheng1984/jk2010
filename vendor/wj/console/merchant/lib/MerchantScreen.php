<?php
abstract class MerchantScreen extends ConsoleScreen {
  protected function renderConsoleContent() {
    echo '<ul id="navigator">';
    if ($_SERVER['REQUEST_URI'] === '/') {
      echo '<li class="selected home"><span>首页</span></li>';
    } else {
      echo '<li><a href="/">首页</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/report') {
      echo '<li class="selected"><span>效果报表</span></li>';
    } else {
      echo '<li><a href="/report">效果报告</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/payment') {
      echo '<li class="selected"><span>结算</span></li>';
    } else {
      echo '<li><a href="/payment">结算</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/active_order') {
      echo '<li class="selected"><span>活跃订单</span></li>';
    } else {
      echo '<li><a href="/active_order">活跃订单</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/io') {
      echo '<li class="selected"><span>数据接口</span></li>';
    } else {
      echo '<li><a href="/io">数据接口</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/account') {
      echo '<li class="selected"><span>帐户设置</span></li>';
    } else {
      echo '<li><a href="/account">帐户设置</a></li>';
    }
    echo '</ul>';
    $this->renderMerchantContent();
  }

  protected function getRole() {
    return '商家';
  }

  abstract protected function renderMerchantContent();
}