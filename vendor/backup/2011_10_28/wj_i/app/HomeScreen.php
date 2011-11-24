<?php
class HomeScreen extends Screen {
  public function renderHeadContent() {
    echo '<title>我的应用 - 货比万家</title>';
    echo '<link type="text/css" href="/css/home.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  public function renderBodyContent() {
    echo '<div id="breadcrumb"><a href="http://huobiwanjia.com">首页</a> &rsaquo; <strong>我的应用</strong></div>';
    echo '<h1>我的应用</h1>';
    echo '<div id="application">';
    echo '<ul>';
    echo '<li><a href="http://activity.wj.com">动态</a></li>';
    echo '<li><a href="http://tracking.wj.com">关注</a></li>';
    echo '<li><a href="http://share.wj.com">分享</a></li>';
    echo '<li><a href="http://message.wj.com">留言</a></li>';
    echo '<li><a href="http://advertisement.wj.com">广告</a></li>';
    echo '<li><a href="http://integration.wj.com">数据整合</a></li>';
    echo '<li><a href="http://analytics.wj.com">访客分析</a></li>';
    echo '<li><a href="http://account.wj.com">帐号设置</a></li>';
    echo '</ul>';
    echo '</div>';
    echo '<div id="activity">';
    echo '<h2>最近动态</h2>';
    echo '</div>';
  }
}