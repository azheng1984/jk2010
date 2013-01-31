<?php
class HomeScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 攻略聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<p>[优选集攻略]</p>';
    echo '<h2>攻略分类</h2>';
    echo '<p>全部</p>';
    echo '<p><a href="/category-1/">装修</a></p>';
    echo '<h2>列表</h2>';
    echo '<p><a href="/article-1/">优选集</a></p>';
    echo '<p>广告</p>';
  }
}