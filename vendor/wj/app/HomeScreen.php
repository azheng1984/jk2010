<?php
class HomeScreen extends Screen {
  public function __construct() {
    header('Cache-Control: private, max-age=0');
  }

  protected function renderHtmlHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('home');
    $this->renderJsLink('home');
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home">';
    $this->renderSlogon();
    $this->renderMerchant();
    echo '</div>';
  }

  private function renderSlogon() {
    $slogonConfig = require CONFIG_PATH
      .'home'.DIRECTORY_SEPARATOR.'slogon.config.php';
    echo '<div id="slogon"><span class="arrow"></span><h1>',
      $slogonConfig['merchant_amount'], '个网上商店，',
      $slogonConfig['product_amount'], '万商品，搜索：</h1>';
    $this->renderQueryList($slogonConfig['query_list']);
    echo '</div>';
  }

  private function renderQueryList($queryList) {
    echo ' <ul>';
    foreach ($queryList as $query) {
      echo '<li><a href="/', $query[0], '/">',
        $query[1],'</a> <span>', $query[2], '</span></li>';
    }
    echo '<li><a href="/+i/">&hellip;</a></li></ul>';
  }

  private function renderMerchant() {
    echo '<div id="merchant">';
    $this->renderMerchantList();
    $this->renderMerchantTypeList();
    echo '</div>';
  }

  private function renderMerchantList() {
    echo '<table>';
    for ($i = 0; $i < 5; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        $uri = urlencode('儿童');
        echo '<td><a href="http://www.360buy.com/?source=huobiwanjia"',
          ' target="_blank" rel="nofollow">',
          '<img src="/+/img/logo/360buy.png" /><span>京东商城</span></a>',
          '</td>';
      }
      echo '</tr>';
    }
    echo '</table>';
  }

  private function renderMerchantTypeList() {
    $typeList = require CONFIG_PATH
      .'home'.DIRECTORY_SEPARATOR.'merchant_type_list.config.php';
    echo '<ul><li><span>全部</span></li>';
    foreach ($typeList as $key => $value) {
      echo '<li><a href="/', $key, '" rel="nofollow">',
        $value[0], '</a></li>';
    }
    echo '</ul>';
  }
}