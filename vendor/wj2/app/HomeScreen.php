<?php
class HomeScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('home');
  }

  protected function renderBodyContent() {
    $this->renderSlogon();
    echo '<div id="index">';
    //echo '<h2>搜索</h2>';
    echo '<table>';
    for ($i = 0; $i < 12; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a class="categoty" href="/儿童+胶囊/">儿童 胶囊</a> <span>123</span></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
    $this->renderBlock();
    echo '<script>document.getElementById("search_input").focus()</script>';
  }

  private function renderSlogon() {
    echo '<div id="slogon">',
      '<div class="arrow"></div>', 
      '<h1><span>11421</span> 网上商城，<span>1508 万</span>产品，搜索：</h1>',
      '</div>';
  }

  private function renderBlock() {
    echo '<div id="block"><div class="ensure">品质保证</div><div class="merchant ensure">品牌商城</div>';
        echo '<div id="more"><a href="/+i/">更多 &raquo;</a></div>';
    echo '</div>';
  }
}