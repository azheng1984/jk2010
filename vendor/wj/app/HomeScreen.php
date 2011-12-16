<?php
class HomeScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('home');
  }

  protected function renderBodyContent() {
    echo '<div id="home">';
    $this->renderSlogon();
    $this->renderQueryList();
    $this->renderFooter();
    $this->renderFocusJs();
    echo '</div>';
  }

  private function renderSlogon() {
    echo '<div id="slogon">',
      '<span></span>',
      '<h1>11421个网上商城，1508万产品，搜索：</h1>',
      '</div>';
  }

  private function renderQueryList() {
    echo '<table>';
    for ($i = 0; $i < 20; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        for ($j = 0; $j < 5; ++$j) {
          $uri = urlencode('儿童');
          echo '<td><a href="/', $uri, '/">儿童 胶囊</a> <span>123</span></td>';
        }
      }
      echo '</tr>';
    }
    echo '</table>';
  }

  private function renderFooter() {
    echo '<div class="footer">',
     '<a href="/+i/">更多 &raquo;</a>',
     '<span><em>品质保证</em> 100%</span><span class="left"><em>品牌商城</em> 100%</span>',
     '</div>';
  }

  private function renderFocusJs() {
    echo '<script>document.getElementById("search_input").focus()</script>';
  }
}