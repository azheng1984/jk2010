<?php
abstract class AdministratorScreen extends ConsoleScreen {
  protected function renderConsoleContent() {
    echo '<a href="/"><h1>货比万家 - 管理员</h1></a><div id="toolbar">';
    echo '<span>root | </span>';
    echo '<a href="/sign_out">退出</a>';
    echo '</div>';
    echo '<ul id="navigator">';
      if ($_SERVER['REQUEST_URI'] === '/') {
      echo '<li><span>首页</span></li>';
    } else {
      echo '<li><a href="/">首页</a></li>';
    }
      if ($_SERVER['REQUEST_URI'] === '/publisher_list') {
      echo '<li><span>广告发布商</span></li>';
    } else {
      echo '<li><a href="/publisher_list">广告发布商</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/merchant_list') {
      echo '<li><span>商家</span></li>';
    } else {
      echo '<li><a href="/merchant_list">商家</a></li>';
    }
      if ($_SERVER['REQUEST_URI'] === '/administrator_list') {
      echo '<li><span>管理员</span></li>';
    } else {
      echo '<li><a href="/administrator_list">管理员</a></li>';
    }
    if ($_SERVER['REQUEST_URI'] === '/account') {
      echo '<li><span>帐户设置</span></li>';
    } else {
      echo '<li><a href="/account">帐户设置</a></li>';
    }
    echo '</ul>';
    $this->renderAdministratorContent();
  }

  abstract protected function renderAdministratorContent();
}