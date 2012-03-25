<?php
class HomeScreen extends Screen {
  private $merchantId;
  private $merchant;
  private $slideIndex;

  public function __construct() {
    if (count($GLOBALS['SLIDESHOW']) === 0
      && $GLOBALS['PAGE'] !== 1) {
      $this->stop();
      header('HTTP/1.1 301 Moved Permanently');
      Header('Location: '.$GLOBALS['MERCHANT_TYPE']['path']);
    }
  }

  protected function renderHtmlHeadContent() {
    echo '<title>';
    if ($GLOBALS['MERCHANT_TYPE']['path'] !== '/') {
      echo $GLOBALS['MERCHANT_TYPE'][1], '-';
    }
    echo '货比万家</title>';
    if ($GLOBALS['MERCHANT_TYPE']['path'] === '/') {
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
  }

  private function renderMerchantTypeList() {
    $path = $GLOBALS['MERCHANT_TYPE']['path'];
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
    $this->initializeSlideshow();
    echo '<div id="slideshow">';
    $this->renderSlideWrapper();
    $this->renderMerchantList();
    $this->renderScroll();
    echo '</div>';
  }

  private function initializeSlideshow() {
    $this->parseMerchantId();
    $this->merchant = $GLOBALS['SLIDESHOW'][$this->merchantId];
    $this->parseSlideIndex();
  }

  private function parseMerchantId() {
    if (isset($_GET['merchant_id']) === false
        || is_numeric($_GET['merchant_id']) === false
        || $_GET['merchant_id'] < 1
        || isset($GLOBALS['SLIDESHOW'][intval($_GET['merchant_id'])]) === false) {
      $this->merchantId = key($GLOBALS['SLIDESHOW']);
      return;
    }
    $this->merchantId = intval($_GET['merchant_id']);
  }
  
  private function parseSlideIndex() {
    if (isset($_GET['index']) === false
        || is_numeric($_GET['index']) === false
        || $_GET['index'] < 0
        || isset($GLOBALS['SLIDESHOW'][$this->merchantId]
            ['slide_list'][intval($_GET['index'])]) === false) {
      $this->slideIndex = 0;
      return;
    }
    $this->slideIndex = intval($_GET['index']);
  }

  private function renderSlideWrapper() {
    echo '<div id="slide_wrapper">';
    $this->renderSlide();
    $this->renderMerchant();
    $this->renderSlideList();
    echo '</div>';
  }

  private function renderSlide() {
    $slide = $this->merchant['slide_list'][$this->slideIndex];
    echo '<a id="slide" href="http://', $slide,
      '/" target="_blank">', '<img src="/+/img/slide/',
      $this->merchant['path'], '/', $this->slideIndex, '.jpg"/></a>';
  }

  private function renderMerchant() {
    echo '<a id="merchant" href="http://',
      $this->merchant['uri_format'], '" target="_blank">',
      '@<span>', $this->merchant['name'], '</span></a>';
  }

  private function renderSlideList() {
    if (count($this->merchant['slide_list']) === 1) {
      return;
    }
    echo '<span id="slide_list">';
    $hrefPrefix = '?';
    if ($GLOBALS['PAGE'] !== 1) {
      $hrefPrefix = '?page='.$GLOBALS['PAGE'].'&';
    }
    foreach ($this->merchant['slide_list'] as $index => $slide) {
      if ($index === $this->slideIndex) {
        echo '<span></span>';
        continue;
      }
      echo '<a href="', $hrefPrefix , 'merchant_id=', $this->merchantId,
        '&index=', $index, '"></a>';
    }
    echo '</span>';
  }

  private function renderMerchantList() {
    $hrefPrefix = '?';
    if ($GLOBALS['PAGE'] !== 1) {
      $hrefPrefix = '?page='.$GLOBALS['PAGE'].'&';
    }
    echo '<div id="merchant_list">';
    foreach ($GLOBALS['SLIDESHOW'] as $id => $merchant) {
      $img = '<img src="/+/img/logo/'.$merchant['path'].'.png"/>';
      if ($id === $this->merchantId) {
        echo '<span>', $img, '</span>';
        continue;
      }
      echo '<a href="', $hrefPrefix, 'merchant_id=', $id, '">', $img, '</a>';
    }
    echo '</div>';
  }

  private function renderScroll() {
    $previous = null;
    $next = null;
    $previousClass = ' class="full"';
    $nextClass = $previousClass;
    if ($GLOBALS['PAGE'] > 1) {
      $previous = $GLOBALS['PAGE'] - 1;
      $nextClass = '';
    }
    if ($GLOBALS['PAGE'] < $GLOBALS['MERCHANT_TYPE'][2]) {
      $next = $GLOBALS['PAGE'] + 1;
      $previousClass = '';
    }
    echo '<div id="scroll">';
    if ($previous !== null) {
      $href = $previous === 1 ? '/' : '?page='.$previous;
      echo '<a id="previous"', $previousClass, ' href="', $href, '"></a>';
    }
    if ($next !== null) {
      echo '<a', $nextClass, ' href="?page=', $next, '"></a>';
    }
    echo '</div>';
  }

  private function addJsConfig() {
    $this->addJs('huobiwanjia.home.slideshow = ['
      .'["京东商城0","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]],'
      .'["京东商城1","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]],'
      .'["京东商城2","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]],'
      .'["京东商城3","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]],'
      .'["京东商城4","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]]'
    .'];');
  }
}