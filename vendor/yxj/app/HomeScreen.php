<?php
class HomeScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 分享智慧</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<p>分享智慧 [优选集指南]</p>';
    echo '<h2>指南分类</h2>';
    echo '<p>全部</p>';
    echo '<p><a href="/category/1/">装修</a></p>';
    echo '<h2>指南列表</h2>';
    echo '<p><a href="/book/1/">优选集</a></p>';
    echo '<p>广告</p>';
  }
}