<?php
class HomeScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('home');
  }

  protected function renderBodyContent() {
    echo '<div id="h1_wrapper"><div></div><h1>在 11421 家网上商城，1508 万个产品中搜索：</h1></div>';
    echo '<ul>';
    for ($i = 0; $i < 7; ++$i) {
      echo '<li class="l',$i,'">',
      '<a href="javascript:void(0)" class="a_img"><img alt="迷宗蟹" src="/product.jpg" /></a>',
      '<div class="keywords"><div class="category"><a href="javascript:void(0)">迷宗蟹</a></div>',
      '<div><a href="javascript:void(0)">耐克</a></div> <div><a href="javascript:void(0)">回力</a></div>',
      '<div><a href="javascript:void(0)">耐克</a></div> <div><a href="javascript:void(0)">回力</a></div>',
      '<div><a href="javascript:void(0)">耐克</a></div> <div><a href="javascript:void(0)">回力</a></div>',
      '<div><a href="javascript:void(0)">耐克</a></div> <div><a href="javascript:void(0)">回力</a></div> <div><a href="javascript:void(0)">...</a></div></div>',
      
      '</li>';
    }
    echo '</ul>';
    echo '<div id="more"><strong><a href="/+i/">更多 &raquo;</a></strong></div>';
    echo '<script>document.getElementById("search_input").focus()</script>';
  }
}