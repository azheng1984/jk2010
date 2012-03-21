<?php
class HomeScreen extends Screen {
  public function __construct() {
    if (count($GLOBALS['MERCHANT_SLIDE_LIST']) === 0
      && $GLOBALS['PAGE'] !== 1) {
      $this->stop();
      header('HTTP/1.1 301 Moved Permanently');
      Header('Location: '.$GLOBALS['MERCHANT_TYPE_CONFIG']['path']);
    }
    $this->parseMerchantId();
    $this->parseSlideIndex();
  }

  private function parseSlideIndex() {
    if (isset($_GET['index']) === false
      || is_numeric($_GET['index']) === false
      || $_GET['index'] < 1) {
      $GLOBALS['SLIDE_INDEX'] = 1;
      return;
    }
    $GLOBALS['SLIDE_INDEX'] = intval($_GET['index']);
  }

  private function parseMerchantId() {
    if (isset($_GET['merchant_id']) === false
      || is_numeric($_GET['merchant_id']) === false
      || $_GET['merchant_id'] < 1) {
      return;
    }
    $GLOBALS['MERCHANT_ID'] = intval($_GET['merchant_id']);
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
    $this->renderMerchantSlideList();
    echo '</div>';
    $this->addJsConfig();
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
    echo '<ol>';
    foreach ($GLOBALS['HOME_CONFIG']['merchant_type_list'] as $key => $value) {
      if ($key === $path) {
        echo '<li class="current">', $value[1], '</li>';
        continue;
      }
      echo '<li><a href="', $key, '" rel="nofollow">', $value[1], '</a></li>';
    }
    echo '</ol>';
  }

  private function renderSlideshow() {
    echo '<div id="slideshow">',
    $this->renderSlide();
    $this->renderToolbar();
    echo '</div>';
  }

  private function renderSlide() {
    echo '<div id="slide">',
      '<a href="http://www.360buy.com/"><img src="/+/img/slide.jpg"/></a>',
      '</div>';
  }

  private function renderToolbar() {
    echo '<div id="toolbar">',
      '<a href="?index=1"></a><a href="?index=2"></a>',
      '<a id="merchant" href="">@<span>京东商城</span></a>',
      '</div>';
  }

  private function renderMerchantList() {
    echo '<div id="merchant_list">',
    $this->renderMerchant();
    $this->renderScroll();
    echo '</div>';
  }

  private function renderMerchant() {
    echo '<a href="?merchant_id=1">',
      '<img src="/+/img/logo/360buy.png"/><span>京东商城</span>',
      '</a>';
  }

  private function renderScroll() {
    echo '<div id="up"></div><div id="down"></div>';
  }

  private function addJsConfig() {
    $this->addJs('var home = { merchant_list:"adf" };');
  }
}