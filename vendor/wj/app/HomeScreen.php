<?php
class HomeScreen extends Screen {
  public function renderBodyContent() {
    echo '<div id="h2_wrapper"><h2>在 3,233,221 个产品中搜索：</h2></div>';
    echo '<ol>';
    for ($i = 0; $i < 32; ++$i) {
      echo '<li><a href="javascript:void(0)"><img alt="鞋子" src="/product.jpg" /></a><a class="categoty" href="javascript:void(0)">鞋子</a><div><a href="javascript:void(0)">耐克</a> <a href="javascript:void(0)">回力</a> <a href="javascript:void(0)">...</a></div></li>';
    }
    echo '</ol>';
    echo '<div id="more"><h2><a href="javascript:void(0)">更多 &raquo;</h2></div>';
    echo '<script>document.getElementById("search_input").focus()</script>';
  }

  public function renderHeadContent() {
    echo '<title>货比万家</title>';
    echo '<link type="text/css" href="/css/home.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }
}