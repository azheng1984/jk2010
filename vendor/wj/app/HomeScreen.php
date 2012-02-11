<?php
class HomeScreen extends Screen {
  private $config;
  private $path;
  private $merchantList;
  private $merchantIndex;

  public function __construct() {
    $this->config = require CONFIG_PATH.'home.config.php';
    if ($GLOBALS['PATH_SECTION_LIST'][1] === '') {
      $this->merchantList = $this->config['merchant_list'];
      $this->path = null;
      $this->merchantIndex = null;
      return;
    }
    $this->path = $GLOBALS['PATH_SECTION_LIST'][1];
    if (!isset(
      $this->config['merchant_index_list'][$this->path]
    )) {
      throw new NotFoundException;
    }
    $this->merchantIndex = $this->config['merchant_index_list'][$this->path];
    $this->merchantList = DbHomeMerchant::getList($this->merchantIndex[0]);
  }

  protected function renderHtmlHeadContent() {
    echo '<title>货比万家</title>',
      '<meta name="description" content="货比万家购物搜索引擎，',
      '商品信息100%来自公司经营（B2C）的正规商店-网上购物，货比万家！"/>';
    $this->addCssLink('home');
    $this->addJsLink('home');
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home">';
    $this->renderSlogon();
    $this->renderMerchant();
    echo '</div>';
    if ($this->path !== null) {
      $this->addJs('merchant_amount='.$this->merchantIndex[2].';');
    }
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
    $this->renderMerchantTypeList();
    $this->renderMerchantList();
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
          echo '<td><a href="http://', $item['uri'], '"',
          ' target="_blank" rel="nofollow">', '<img alt="', $item['name'],
          '" src="/+/img/logo/', $item['path'], '.png"/><span>',
          $item['name'], '</span></a></td>';
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
    if ($this->path === null) {
      echo '<span>全部</span>';
    } else {
      echo '<a href="/">全部</a>';
    }
    echo '</li>';
    foreach ($this->config['merchant_index_list'] as $key => $value) {
      if ($key === $this->path) {
        echo '<li><span>', $value[1], '</span></li>';
        continue;
      }
      echo '<li><a href="/', $key, '" rel="nofollow">',
      $value[1], '</a></li>';
    }
    echo '</ul>';
  }
}