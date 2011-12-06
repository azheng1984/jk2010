<?php
class HomeScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('home');
  }

  protected function renderBodyContent() {
    $this->renderSlogon();
    echo '<ul>';
    for ($i = 0; $i < 7; ++$i) {
      echo '<li class="l', $i, '">',
      '<a href="javascript:void(0)"><img alt="迷宗蟹" src="/img/product.jpg" /></a>',
      '<strong><a href="javascript:void(0)">迷宗蟹</a></strong>';
      for ($j = 0; $j < 8; ++$j) {
        echo ' <a href="javascript:void(0)">阿迪达斯</a>';
      }
      echo ' <a href="javascript:void(0)">...</a>',
      '</li>';
    }
    echo '</ul>';
    echo '<h2><a href="/+i/">更多 &raquo;</a></h2>';
    echo '<script>document.getElementById("search_input").focus()</script>';
  }

  private function renderSlogon() {
    echo '<div id="h1_wrapper">',
      '<div id="slogon_arrow"></div>',
      '<h1>在 11421 家网上商城，1508 万个产品中搜索：</h1>',
      '</div>';
  }
}