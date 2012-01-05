<?php
class HomeScreen extends Screen {
  public function __construct() {
    header('Cache-Control: max-age=3600');
  }

  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('home');
  }

  protected function renderBodyContent() {
    echo '<div id="home">';
    $this->renderSlogon();
    $this->renderMerchant();
    $this->renderMore();
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
    echo '<div id="merchant_list">';
    $this->renderMerchantList();
    $this->renderMerchantTypeList();
    echo '</div>';
  }

  private function renderMerchantList() {
    echo '<table>';
    for ($i = 0; $i < 6; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        $uri = urlencode('儿童');
        echo '<td><a target="_blank"',
          ' href="http://www.360buy.com/?source=huobiwanjia" rel="nofollow">',
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
    foreach ($typeList as $type) {
      echo '<li><a href="/', $type[1], '" rel="nofollow">',
        $type[0], '</a></li>';
    }
    echo '</ul>';
  }

  private function renderMore() {
    echo '<div id="more">',
     '<div><a href="javascript:void(0)">更多商店</a> 1/12</div>',
     '<span>100%公司经营</span><span class="left">100%正规商店</span>',
     '</div>';
  }
}