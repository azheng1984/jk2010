<?php
class HomeScreen extends Screen {
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
    $this->renderMerchantBlock();
    echo '</div>';
    if (isset($GLOBALS['MERCHANT_TYPE'])) {
      $this->addJs('merchant_amount='.$GLOBALS['MERCHANT_TYPE'][2].';');
    }
  }

  private function renderSlogon() {
    echo '<div id="slogon"><span class="arrow"></span><h1>',
      $GLOBALS['HOME_CONFIG']['merchant_amount'], '个网上商店，',
      $GLOBALS['HOME_CONFIG']['product_amount'], '万商品，搜索：</h1>';
    $this->renderQueryList();
    echo '</div>';
  }

  private function renderQueryList() {
    echo '<ul>';
    foreach ($GLOBALS['HOME_CONFIG']['query_list'] as $query) {
      echo '<li><a href="/', urlencode($query[0]), '/">',
      $query[1], '</a> ', $query[2], '</li>';
    }
    echo '<li><a href="/+i/">&hellip;</a></li></ul>';
  }

  private function renderMerchantBlock() {
    echo '<div id="merchant">';
    $this->renderMerchantTypeList();
    $this->renderMerchantList();
    echo '</div>';
  }

  private function renderMerchantTypeList() {
    $path = null;
    if (isset($GLOBALS['MERCHANT_TYPE'])) {
      $path = $GLOBALS['MERCHANT_TYPE']['path'];
    }
    echo '<ul>';
    $this->renderAllMerchantIndex();
    foreach ($GLOBALS['HOME_CONFIG']['merchant_type_list'] as $key => $value) {
      if ($key === $path) {
        echo '<li><span>', $value[1], '</span></li>';
        continue;
      }
      echo '<li><a href="/', $key, '" rel="nofollow">', $value[1], '</a></li>';
    }
    echo '</ul>';
  }

  private function renderAllMerchantIndex() {
    if (isset($GLOBALS['MERCHANT_TYPE']) === false) {
      echo '<li><span>全部</span></li>';
      return;
    }
    echo '<li><a href="/">全部</a></li>';
  }

  private function renderMerchantList() {
    echo '<table>';
    $index = 0;
    $amount = count($GLOBALS['MERCHANT_LIST']);
    for ($row = 0; $row < 5; ++$row) {
      echo '<tr>';
      for ($column = 0; $column < 5; ++$column, ++$index) {
        if ($amount > $index) {
          $item = $GLOBALS['MERCHANT_LIST'][$index];
          echo '<td><a href="http://', $item['uri'],
            '" target="_blank" rel="nofollow"><img alt="', $item['name'],
            '" src="/+/img/logo/', $item['path'], '.png"/><span>',
            $item['name'], '</span></a></td>';
          continue;
        }
        echo '<td></td>';
      }
      echo '</tr>';
      if ($index >= $amount) {
        break;
      }
    }
    echo '</table>';
  }
}