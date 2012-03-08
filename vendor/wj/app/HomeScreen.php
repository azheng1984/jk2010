<?php
class HomeScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>';
    if (isset($GLOBALS['MERCHANT_TYPE'])) {
      echo $GLOBALS['MERCHANT_TYPE'][1], '-';
    }
    echo '货比万家</title>';
    if (isset($GLOBALS['MERCHANT_TYPE']) === false) {
      echo '<meta name="description" content="货比万家购物搜索引擎，',
        '商品信息100%来自公司经营（B2C）的正规商店-网上购物，货比万家！"/>';
    }
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
    echo '<div id="slogon"><h1>',
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
    echo '<li><a href="/+i/">…</a></li></ul>';
  }

  private function renderMerchantBlock() {
    echo '<div id="merchant">';
    $this->renderMerchantTypeList();
    $this->renderMerchantList();
    $this->renderPagination();
    echo '</div>';
  }

  private function renderMerchantTypeList() {
    $path = '/';
    if (isset($GLOBALS['MERCHANT_TYPE'])) {
      $path = $GLOBALS['MERCHANT_TYPE']['path'];
    }
    echo '<ul>';
    foreach ($GLOBALS['HOME_CONFIG']['merchant_type_list'] as $key => $value) {
      if ($key === $path) {
        echo '<li><span>', $value[1], '</span></li>';
        continue;
      }
      echo '<li><a href="', $key, '" rel="nofollow">', $value[1], '</a></li>';
    }
    echo '</ul>';
  }

  private function renderMerchantList() {
    $index = 0;
    echo '<table><tr>';
    foreach ($GLOBALS['MERCHANT_LIST'] as $merchant) {
      if ($index % 5 === 0 && $index !== 0) {
        echo '</tr><tr>';
      }
      echo '<td><a href="http://', $merchant['uri'],
        '" target="_blank" rel="nofollow"><img alt="', $merchant['name'],
        '" src="/+/img/logo/', $merchant['path'], '.png"/><span>',
        $merchant['name'], '</span></a></td>';
      ++$index;
    }
    if ($index % 5 !== 0 && $index > 5) {
      $colspan = 5 - $index % 5;
      $colspanAttribute = $colspan === 1 ? '' : ' colspan="'.$colspan.'"';
      echo '<td', $colspanAttribute, '></td>';
    }
    echo '</tr></table>';
  }

  //TODO:第一页链接和 js 处理后外观保持一致，第二页开始使用标准分页（no script），全部 nofollow
  private function renderPagination() {
    $path = '/';
    if (isset($GLOBALS['MERCHANT_TYPE'])) {
      $path = $GLOBALS['MERCHANT_TYPE']['path'];
    }
    $merchantAmount = $GLOBALS['HOME_CONFIG']['merchant_type_list'][$path][2];
    if ($merchantAmount > 20) {
      echo '<div id="pagination_wrapper"><a id="more" href="?page=2" rel="nofollow"><span>更多</span></a>1/10</div>';
    }
  }
}