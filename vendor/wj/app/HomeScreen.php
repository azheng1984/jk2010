<?php
class HomeScreen extends Screen {
  public function __construct() {
    if (count($GLOBALS['MERCHANT_LIST']) === 0 && $GLOBALS['PAGE'] !== 1) {
      $this->stop();
      header('HTTP/1.1 301 Moved Permanently');
      Header('Location: '.$GLOBALS['MERCHANT_TYPE_CONFIG']['path']);
    }
  }

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
    $this->renderActivitySlideshow();
    $this->renderMerchantList();
    $this->renderPagination();
    echo '</div>';
  }

  private function renderActivitySlideshow() {
    echo '<div id="slideshow"><div id="activity"><a class="image_link" href="http://www.360buy.com/"><img src="/+/img/activity.jpg" /></a><br /><a id="title" href="http://www.360buy.com/">我跟春天有个“优惠” - 京东商城</a></div>',
      '<div id="toolbar"><span class="left"></span> <span class="page"><span class="current"></span> <span></span> <span></span> <span></span> <span></span></span><span class="right">更多活动</span></div>',
      '</div>';
  }

  private function renderMerchantTypeList() {
    $path = $GLOBALS['MERCHANT_TYPE_CONFIG']['path'];
    echo '<ul>';
    foreach ($GLOBALS['HOME_CONFIG']['merchant_type_list'] as $key => $value) {
      if ($key === $path) {
        echo '<li class="current">', $value[1], '</li>';
        continue;
      }
      echo '<li><a href="', $key, '" rel="nofollow">', $value[1], '</a></li>';
    }
    echo '</ul>';
  }

  private function renderMerchantList() {
    $index = 0;
    echo '';
    echo '<div id="list_wrapper"><table><tr>';
    foreach ($GLOBALS['MERCHANT_LIST'] as $merchant) {
      if ($index % 5 === 0 && $index !== 0) {
        echo '</tr><tr>';
      }
      echo '<td><a href="http://', $merchant['uri'], '"',
        ' target="_blank" rel="nofollow"><img alt="',
        $merchant['name'], '" src="/+/img/logo/', $merchant['path'], '.png"/>',
        '<span>', $merchant['name'], '</span></a></td>';
      ++$index;
    }
    if ($index % 5 !== 0 && $index > 5) {
      $colspan = 5 - $index % 5;
      $colspanAttribute = $colspan === 1 ? '' : ' colspan="'.$colspan.'"';
      echo '<td', $colspanAttribute, '></td>';
    }
    echo '</tr></table><div class="move">»</div></div>';
  }

  private function renderPagination() {
    $amount = $GLOBALS['MERCHANT_TYPE_CONFIG'][2];
    if ($amount < 25) {
      return;
    }
    if ($GLOBALS['PAGE'] === 1) {
      echo '<noscript><a id="more" href="?page=2" rel="nofollow"><span>更多商店</span></a></noscript>';
      return;
    }
    PaginationScreen::render($GLOBALS['PAGE'], $amount, '?page=', '', 10, 25);
  }
}