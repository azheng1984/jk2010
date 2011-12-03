<?php
class HomeScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('home');
  }

  protected function renderBodyContent() {
    echo '<div id="h1_wrapper"><div></div><h1>在 11421 家网上商城，1508 万个产品中搜索：</h1></div>';
    echo '<ol>';
    for ($i = 0; $i < 28; ++$i) {
      echo '<li><a href="javascript:void(0)"><img alt="鞋子" src="/product.jpg" /></a><a class="categoty" href="javascript:void(0)">鞋子</a><div><a href="javascript:void(0)">耐克</a> <a href="javascript:void(0)">回力</a> <a href="javascript:void(0)">...</a></div></li>';
    }
    echo '</ol>';
    echo '<div id="more"><a href="/+i/">更多 &raquo;</a></div>';
    echo '<script>document.getElementById("search_input").focus()</script>';
  }
}