<?php
class HomeScreen extends Screen {
  private $config;
  private $merchantList;

  public function __construct() {
    $this->initailize();
    header('Cache-Control: private, max-age=0');
  }

  private function initailize() {
    $this->config = require CONFIG_PATH.'home.config.php';
    if (!isset($GLOBALS['URI']['MERCHANT_INDEX_NAME'])) {
      $this->merchantList = $this->config['merchant_list'];
      return;
    }
    $name = $GLOBALS['URI']['MERCHANT_INDEX_NAME'];
    if (!isset($this->config['merchant_index_list'][$name])) {
      throw new NotFoundException;
    }
    $merchantList = DbMerchantHome::getList(
      $this->config['merchant_index_list'][$name][0]
    );
    $this->merchantList = array(array(
      $merchantList[0]['name'],
      $merchantList[0]['uri'],
      $merchantList[0]['uri_section']
    ));
  }

  protected function renderHtmlHeadContent() {
    echo '<title>货比万家</title>',
      '<meta name="description" content="货比万家购物搜索引擎，',
      '商品信息100%来自公司经营（B2C）的正规商店-网上购物，货比万家！"/>';
    $this->addCssLink('home');
    $this->addJsLink('home');
    if (isset($GLOBALS['URI']['MERCHANT_INDEX_NAME'])) {
      $this->addJs('merchant_amount='.$this->config['merchant_index_list'][$GLOBALS['URI']['MERCHANT_INDEX_NAME']][2].';');
    }
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
    $amount = count($this->merchantList);
    for ($row = 0; $row < 5; ++$row) {
      echo '<tr>';
      for ($column = 0; $column < 5; ++$column) {
        if ($amount > $index) {
          $item = $this->merchantList[$index];
          echo '<td><a href="http://', $item[1], '"',
          ' target="_blank" rel="nofollow">', '<img alt="', $item[0],
          '" src="/+/img/logo/', $item[2], '.png"/><span>',
          $item[0], '</span></a></td>';
        } else {
          echo '<td></td>';
        }
        ++$index;
      }
      echo '</tr>';
      if ($amount < $index) {
        break;
      }
    }
    echo '</table>';
  }

  private function renderMerchantTypeList() {
    echo '<ul><li>';
    if (!isset($GLOBALS['URI']['MERCHANT_INDEX_NAME'])) {
      echo '<span>全部</span>';
    } else {
      echo '<a href="/">全部</a>';
    }
    echo '</li>';
    $merchantIndexName = null;
    if (isset($GLOBALS['URI']['MERCHANT_INDEX_NAME'])) {
      $merchantIndexName = $GLOBALS['URI']['MERCHANT_INDEX_NAME'];
    }
    foreach ($this->config['merchant_index_list'] as $key => $value) {
      if ($key === $merchantIndexName) {
        echo '<li><span>', $value[1], '</span></li>';
        continue;
      }
      echo '<li><a href="/', $key, '" rel="nofollow">',
        $value[1], '</a></li>';
    }
    echo '</ul>';
  }
}