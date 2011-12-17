<?php
class HomeScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('home');
  }

  protected function renderBodyContent() {
    echo '<div id="home">';
    $this->renderSlogon();
    $this->renderMerchantList();
    $this->renderCategoryList();
    $this->renderFooter();
    $this->renderFocusJs();
    echo '</div>';
  }

  private function renderSlogon() {
    echo '<div id="slogon">',
      '<span class="arrow"></span>',
      '<h1>11421个网上商店，1508万产品，搜索：</h1>';
    $this->renderQueryList();
    echo '</div>';
  }

  private function renderQueryList() {
    $uri = urlencode('儿童');
    echo ' <ul>',
      '<li><a href="/', $uri, '/">儿童 胶囊</a> <span>123</span>',
      '<li><a href="/', $uri, '/">儿童 胶囊</a> <span>123</span>',
      '<li><a href="/', $uri, '/">儿童 胶囊</a> <span>123</span>',
      '<li><a href="/', $uri, '/">儿童 胶囊</a> <span>123</span>',
      '<li><a href="/', $uri, '/">儿童 胶囊</a> <span>123</span>',
      '<li><a href="/', $uri, '/">儿童 胶囊</a> <span>123</span>',
      '<li><a href="/', $uri, '/">儿童 胶囊</a> <span>123</span>',
      '</ul>';
    echo '<strong><a class="more" href="/+i/">更多 &raquo;</a></strong>';
  }

  private function renderMerchantList() {
    echo '<table>';
    for ($i = 0; $i < 12; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 4; ++$j) {
          $uri = urlencode('儿童');
          echo '<td><a target="_blank" href="http://www.360buy.com/?source=huobiwanjia" rel="nofollow">京东商城</a></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
  }
  private function renderCategoryList() {
    $uri = urlencode('儿童');
    echo ' <ul id="category">';
    echo '<li><span>全部</span></li>';
    echo '<li><a href="?type=综合">百货</a></li>';
    echo '<li><a href="?type=综合">数码家电</a></li>';
    echo '<li><a href="?type=综合">服装鞋帽</a></li>';
    echo '<li><a href="?type=综合">奢侈品</a></li>';
    echo '</ul>';
  }

  private function renderFooter() {
    echo '<div class="footer">',
     '<a href="javascript:void(0)">更多 &raquo;</a>',
     '<span><em>公司经营</em> 100%</span><span class="left"><em>品牌名店</em> 100%</span>',
     '</div>';
  }

  private function renderFocusJs() {
    echo '<script>document.getElementById("search_input").focus()</script>';
  }
}