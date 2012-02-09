<?php
class HomeScreen extends Screen {
  private $config;

  public function __construct() {
    header('Cache-Control: private, max-age=0');
    $this->config = require CONFIG_PATH.'home.config.php';
  }

  protected function renderHtmlHeadContent() {
    echo '<title>货比万家</title>',
      '<meta name="description" content="货比万家购物搜索引擎，',
      '商品信息100%来自公司经营（B2C）的正规商店-网上购物，货比万家！"/>';
    $this->addCssLink('home');
    $this->addJsLink('home');
    $this->addJs('merchant_amount=124;');//TODO:reader by config
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home">';
    $this->renderSlogon();
    $this->renderMerchant();
    echo '</div>';
  }

  private function renderSlogon() {
    echo '<div id="slogon"><span class="arrow"></span><h1>',
      $this->config['merchant_amount'], '个网上商店，',
      $this->config['product_amount'], '万商品，搜索：</h1>';
    $this->renderQueryList();
    echo '</div>';
  }

  private function renderQueryList() {
    echo ' <ul>';
    foreach ($this->config['query_list'] as $query) {
      echo '<li><a href="/', urlencode($query[0]), '/">',
        $query[1],'</a> ', $query[2], '</li>';
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
    $index = 0;
    //TODO: 非 home 情况（商家分类索引）
    $merchantList = $this->config['merchant_list'];
    for ($row = 0; $row < 5; ++$row) {
      echo '<tr>';
      for ($column = 0; $column < 5; ++$column) {
        $item = $merchantList[$index];
        echo '<td><a href="http://', $item[1], '"',
          ' target="_blank" rel="nofollow">', '<img alt="', $item[0],
          '" src="/+/img/logo/', $item[2], '.png"/><span>',
          $item[0], '</span></a></td>';
        ++$index;
      }
      echo '</tr>';
    }
    echo '</table>';
  }

  private function renderMerchantTypeList() {
    echo '<ul><li><span>全部</span></li>';
    foreach ($this->config['merchant_type_list'] as $key => $value) {
      echo '<li><a href="/', $key, '" rel="nofollow">',
        $value[0], '</a></li>';
    }
    echo '</ul>';
  }
}