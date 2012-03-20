<?php
class HomeScreen extends Screen {
  public function __construct() {
    if (count($GLOBALS['MERCHANT_SLIDE_LIST']) === 0
      && $GLOBALS['PAGE'] !== 1) {
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
    $this->addJsConfig();
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home">';
    $this->renderSlogon();
    $this->renderMerchantSlideList();
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
    echo '<li class="ellipsis">…</li></ul>';
  }

  private function renderMerchantSlideList() {
    $this->renderMerchantTypeList();
    $this->renderSlideshow();
    $this->renderMerchantList();
  }

  private function renderMerchantTypeList() {
    $path = $GLOBALS['MERCHANT_TYPE_CONFIG']['path'];
    echo '<ul id="type_list">';
    foreach ($GLOBALS['HOME_CONFIG']['merchant_type_list'] as $key => $value) {
      if ($key === $path) {
        echo '<li class="current">', $value[1], '</li>';
        continue;
      }
      echo '<li><a href="', $key, '" rel="nofollow">', $value[1], '</a></li>';
    }
    echo '</ul>';
  }

  private function renderSlideshow() {
    echo '<div id="slideshow">',
    '</div>';
  }

  private function renderMerchantList() {
    echo '<ol id="merchant_list">',
    '</ol>';
  }

  private function addJsConfig() {
    
  }
}