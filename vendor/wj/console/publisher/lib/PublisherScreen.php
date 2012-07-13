<?php
abstract class PublisherScreen extends ConsoleScreen {
  protected function renderConsoleContent() {
    echo '<ul id="navigator">';
    if ($_SERVER['REQUEST_URI'] === '/') {
      echo '<li class="home selected" style="border-radius: 3px 0 0 3px;"><span>首页</span></li>';
    } else {
      echo '<li class="home" style="border-radius: 3px 0 0 3px;"><a href="/">首页</a></li>';
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
    if ($_SERVER['REQUEST_URI'] === '/ad_widget') {
      echo '<li class="selected"><span>广告控件</span></li>';
    } else {
      echo '<li><a href="/ad_widget">广告控件</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/account') {
      echo '<li class="selected"><span>帐户设置</span></li>';
    } else {
      echo '<li><a href="/account">帐户设置</a></li>';
    }
    echo '</ul>';
    $this->renderPublisherContent();
  }

  protected function getRole() {
    return '广告发布商';
  }

  abstract protected function renderPublisherContent();
}