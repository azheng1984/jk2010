<?php
class HomeScreen {
  public function render() {
    echo '<div><a href="/">货比万家</a></div>';
    echo '<form><input name="q" /><input type="submit" value="搜索" /></form>';
    echo '<div>referrer</div>';
    echo '<h1>xxx 个网上商店，xxx 万商品，搜索：</h1>';
    echo '<ul>';
    echo '<li><a href="/aaa/">aaa</a> [12]</li>';
    echo '<li><a href="/bbb/">bbb</a> [12]</li>';
    echo '</ul>';
    echo '<ul>';
    echo '<li><a href="/aaa/">logo</a> [12]</li>';
    echo '<li><a href="/bbb/">bbb</a> [12]</li>';
    echo '</ul>';
  }
}