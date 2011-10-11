<?php
class HomeScreen {
  public function render() {
    echo '<link type="text/css" href="/home.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<div id="wrapper">';
    echo '<div><a href="http://huobiwanjia.com">首页</a> > 我的应用</div>';
    echo '<h1>我的应用</h1>';
    echo '<div id="application">';
    echo '<ul>';
    echo '<li><a href="http://data.wj.com">商家数据</a></li>';
    echo '<li><a href="http://publisher.wj.com">广告</a></li>';
    echo '</ul>';
    echo '</div>';
    echo '<div id="activity">';
    echo '<h2>最新动态</h2>';
    echo '</div>';
    echo '</div>';
  }
}