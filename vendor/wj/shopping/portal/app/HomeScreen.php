<?php
class HomeScreen extends Screen {
  protected function renderHtmlHeadContent() {
  }

  protected function renderHtmlBodyContent() {
    echo '<h1>xxx 个网上商店，xxx 万商品，搜索：</h1>';
    echo '<div>query list</div>';
    echo '<ul>';
    echo '<li><a href="/aaa/">aaa</a> [12]</li>';
    echo '<li><a href="/bbb/">bbb</a> [12]</li>';
    echo '</ul>';
    echo '<div>merchant list</div>';
    echo '<ul>';
    echo '<li><a href="/aaa/">logo</a> [12]</li>';
    echo '<li><a href="/bbb/">bbb</a> [12]</li>';
    echo '</ul>';
    echo '<div>分类</div>';
  }
}