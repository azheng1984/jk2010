<?php
abstract class MerchantScreen extends ConsoleScreen {
  protected function renderConsoleContent() {
    $this->renderMerchantContent();
  }

  protected function renderNav() {
    echo '<ul id="navigator">';
    if ($_SERVER['REQUEST_URI'] === '/') {
      echo '<li class="selected home"><span>首页</span></li>';
    } else {
      echo '<li class="home"><a href="/">首页</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/report') {
      echo '<li class="selected"><span>效果报告</span></li>';
    } else {
      echo '<li><a href="/report">效果报告</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/payment') {
      echo '<li class="selected"><span>结算</span></li>';
    } else {
      echo '<li><a href="/payment">结算</a></li>';
    }
      if ($_SERVER['REQUEST_URI'] === '/io') {
      echo '<li class="selected"><span>数据接口</span></li>';
    } else {
      echo '<li><a href="/io">数据接口</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/data_optimization') {
      echo '<li class="selected"><span>数据优化</span></li>';
    } else {
      echo '<li><a href="/data_optimization">数据优化</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/account') {
      echo '<li class="selected last"><span>帐户设置</span></li>';
    } else {
      echo '<li class="last"><a href="/account">帐户设置</a></li>';
    }
    echo '</ul>';
  }

  protected function getRole() {
    return '商家';
  }

  abstract protected function renderMerchantContent();
}