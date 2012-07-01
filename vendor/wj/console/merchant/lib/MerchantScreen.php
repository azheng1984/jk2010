<?php
abstract class MerchantScreen extends ConsoleScreen {
  protected function renderConsoleContent() {
    echo '<a href="/"><h1>货比万家 - 商家</h1></a><div id="toolbar">';
    echo '<span>root | </span>';
    echo '<a href="/sign_out">退出</a>';
    echo '</div>';
    echo '<ul id="navigator">';
    if ($_SERVER['REQUEST_URI'] === '/') {
      echo '<li><span>首页</span></li>';
    } else {
      echo '<li><a href="/">首页</a></li>';
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
    if ($_SERVER['REQUEST_URI'] === '/io') {
      echo '<li><span>数据接口</span></li>';
    } else {
      echo '<li><a href="/io">数据接口</a></li>';
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