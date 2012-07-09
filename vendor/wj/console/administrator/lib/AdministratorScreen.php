<?php
abstract class AdministratorScreen extends ConsoleScreen {
  protected function renderConsoleContent() {
    echo '<ul id="navigator">';
      if ($_SERVER['REQUEST_URI'] === '/') {
      echo '<li class="selected"><span>首页</span></li>';
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
    if ($_SERVER['REQUEST_URI'] === '/publisher_list') {
      echo '<li class="selected"><span>广告发布商</span></li>';
    } else {
      echo '<li><a href="/publisher_list">广告发布商</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/merchant_list') {
      echo '<li class="selected"><span>商家</span></li>';
    } else {
      echo '<li><a href="/merchant_list">商家</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/administrator_list') {
      echo '<li class="selected"><span>管理员</span></li>';
    } else {
      echo '<li><a href="/administrator_list">管理员</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/account') {
      echo '<li class="selected"><span>帐户设置</span></li>';
    } else {
      echo '<li><a href="/account">帐户设置</a></li>';
    }
    echo '</ul>';
    $this->renderAdministratorContent();
  }

  abstract protected function renderAdministratorContent();

  protected function getRole() {
    return '管理员';
  }
}