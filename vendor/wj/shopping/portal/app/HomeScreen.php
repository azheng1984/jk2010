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
      header('Location: '.$GLOBALS['MERCHANT_TYPE']['path']);
    }
  }

  protected function renderHtmlHeadContent() {
    echo '<title>';
    if ($GLOBALS['MERCHANT_TYPE']['path'] !== '/') {
      echo $GLOBALS['MERCHANT_TYPE'][1], '-';
    }
    echo '货比万家</title>';
    if ($GLOBALS['MERCHANT_TYPE']['path'] === '/') {
      echo '<meta name="description" content="',
        '联合网上商店，为消费者提供商品搜索服务。"/>';
    }
    $this->addCssLink('home');
    $this->addJsLink('home');
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home">';
    $this->renderHome();
    echo '</div>';
    AdSenseScreen::render('1', 'ad bottom');
    $this->addJsConfig();
  }

  private function renderQueryList() {
    echo '<ul>';
    foreach ($GLOBALS['HOME_CACHE']['query_list'] as $query) {
      echo '<li><a href="/', urlencode($query[0]), '/">',
        $query[1], '</a></li>';
    }
    echo '<li class="ellipsis"><span>…</span></li></ul>';
  }

  private function renderHome() {
    echo '<div class="content">';
    $this->renderSlideshow();
    echo '</div>';
  }

  private function renderMerchantTypeList() {
    $path = $GLOBALS['MERCHANT_TYPE']['path'];
    echo '<ol>';
    foreach ($GLOBALS['HOME_CACHE']['merchant_type_list'] as $key => $value) {
      if ($key === $path) {
        echo '<li class="current">', $value[1], '</li>';
        continue;
      }
      echo '<li';
      if ($value[1] === '更多分类') {
        echo ' id="more_category"';
      }
      echo '><a href="', $key, '" rel="nofollow">', $value[1], '</a></li>';
    }
    echo '</ol>';
  }

  private function renderSlideshow() {
    $this->initializeSlideshow();
    echo '<div id="slideshow">';
    $this->renderSlideWrapper();

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
        || isset($GLOBALS['SLIDESHOW'][intval($_GET['merchant_id'])])
          === false) {
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
    $this->renderSlogan();
    $this->renderMerchantList();
    echo '</div>';
  }

  private function renderSlogan() {
    echo '<div id="slogon"><div class="content"><h1>',
    $GLOBALS['HOME_CACHE']['merchant_amount'], '个网上商店，',
    $GLOBALS['HOME_CACHE']['product_amount'], '万商品，搜索：</h1>';
    $this->renderQueryList();
    echo '</div></div>';
  }

  private function renderSlide() {
    $slide = $this->merchant['slide_list'][$this->slideIndex];
    //echo '<a id="previous" href="javascript:;"><img id="arrow_left" src="/+/img/arrow_left.png"/></a>';
    echo '<div id="left_img_shadow"></div><a id="slide" href="http://', $slide,
      '/" target="_blank" rel="nofollow">', '<img src="/+asset/img/slide/',
      $this->merchant['path'], '/', $this->slideIndex, '.jpg"/></a>';
    //echo '<a id="next" href="javascript:;"><img id="arrow" src="/+/img/arrow.png"/></a>';
  }

  private function renderMerchantList() {
    $hrefPrefix = '?';
    if ($GLOBALS['PAGE'] !== 1) {
      $hrefPrefix = '?page='.$GLOBALS['PAGE'].'&';
    }
    echo '<div id="merchant_list_wrapper"><h2>网上商店推荐：</h2><div id="merchant_list">';
    foreach ($GLOBALS['SLIDESHOW'] as $id => $merchant) {
      $img = '<img src="/+asset/img/logo/'.$merchant['path'].'.png"/>';
//       if ($id === $this->merchantId) {
//         echo '<span>', $img, '</span>';
//         continue;
//       }
      echo '<a title="京东商城" href="', $hrefPrefix, 'merchant_id=', $id, '" rel="nofollow">',
        $img, '</a>';
    }
    echo '</div></div>';
  }

  private function addJsConfig() {
    $list = array();
    foreach ($GLOBALS['SLIDESHOW'] as $item) {
      $list[] = '["'.$item['name'].'","'.$item['uri_format'].'","'
        .$item['path'].'",["'.implode('","', $item['slide_list']).'"]]';
    }
    $this->addJs('huobiwanjia.home.slideshow={merchantAmount:'
      .$GLOBALS['MERCHANT_TYPE'][2].',merchantList:['.implode(',', $list).']};'
    );
  }
}