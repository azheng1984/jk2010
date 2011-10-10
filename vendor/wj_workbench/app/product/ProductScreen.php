<?php
class ProductScreen extends Screen {
  private $category;
  private $product;

  public function __construct() {
    if (($index = DbProduct::getIndex($_GET['product_id'])) === false) {
      throw new NotFoundException;
    }
    $this->category = DbCategory::get($index['category_id']);
    $this->product = DbProduct::get(
      $this->category['table_prefix'], $index['product_id']
    );
  }

  protected function renderHeadContent() {
    echo '<title>'.$this->product['name'].' - 货比万家</title>';
    echo '<link type="text/css" href="/css/home.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<link type="text/css" href="/css/category_list.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<link type="text/css" href="/css/product_list.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<link type="text/css" href="/css/product.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  protected function renderBodyContent() {
    $categories = array($this->category);
    $category = $this->category;
    while ($category['parent_id'] !== '0') {
      $category = DbCategory::get($category['parent_id']);
      array_unshift($categories, $category);
    }
    $breadcrumb = new Breadcrumb($categories, $this->product);
    $breadcrumb->render();
    echo '<div id="h1_wrapper">';
    echo '<h1>'.$this->product['name'].'</h1>';
    echo '<div id="action"><a href="/">对比</a> <a href="/">关注 <span class="note">12134</span></a> <a href="/">分享</a></div>';
    echo '</div>';
    echo '<div id="product">';
    $this->renderPropertyList($categories);
    $this->renderRight();
    echo '</div>';
  }

   private function renderRight() {
    echo '<div id="right_wrapper">';
    echo '<div id="sort_wrapper"><div id="sort">排序: <span>销量</span> <a href="/">价格</a></div>';
    echo '<div id="total">找到 2 个商家</div>';
    echo '</div>';
    $this->renderMerchantList();
    $this->renderAds();
    $this->renderProductList();
    $this->renderAds(true);
    echo '</div>';
   }

  private function renderMerchantList() {
    echo '<div id="merchant_list">';

    echo '<div class="item">';
    echo '<div class="description">';
    echo '<div class="logo"><a rel="nofollow" href="/" target="_blank"><img alt="京东商城" src="/360buy.com.2.gif" /></a></div>';
    echo '<div><a href="/" target="_blank" rel="nofollow"><span class="name">京东商城</span></a></div>';
    echo '</div>';
    echo '<div class="promotion">';
    echo '<div><span class="rmb">&yen;</span><span class="price">10.23</span></div>';
    echo '</div>';
    echo '</div>';

    echo '<div class="item">';
    echo '<div class="description">';
    echo '<div class="logo"><a rel="nofollow" href="/" target="_blank"><img alt="新蛋网" src="/newegg.com.cn.2.gif" /></a></div>';
    echo '<div><a href="/" target="_blank" rel="nofollow"><span class="name">新蛋网</span></a></div>';
    echo '</div>';
    echo '<div class="promotion">';
    echo '<div><span class="rmb">&yen;</span><span class="price">14</span></div>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>';
  }

  private function renderPropertyList($categories) {
    echo '<div id="property_list_wrapper">';
    echo '<img title="'.$this->product['name'].'" alt="'.$this->product['name'].'" src="/x.jpg" />';
    $categoryPath = $this->getCategoryPath($categories);
    echo '<div id="property_list">';
    echo '<div>品牌: <a href="..">诺基亚(Nokia)</a></strong></div>';
    echo '<div>型号: G470AH-ITH</div>';
    foreach (explode(',', $this->product['property_value_list']) as $id) {
      $result = DbProperty::getByValueId($this->category['table_prefix'], $id);
      echo '<div>'.$result['key'].': <a rel="nofollow" href="'.$categoryPath.'?'.urlencode($result['key']).'='.urlencode($result['value']).'">'.$result['value'].'</a></div>';
    }
    echo '</div>';
    echo '</div>';
  }

  private function getCategoryPath($categories) {
    $path = '/';
    foreach ($categories as $category) {
      $path .= urlencode($category['name']).'/';
    }
    return $path;
  }

  private function renderProductList() {
    echo '<div id="product_list_title_wrapper"><div id="product_list_title">';
    echo '<h2>浏览 "'.$this->product['name'].'" 的顾客最终购买</h2>';
    echo '<div id="buy_rate"><div class="rate_wrapper"><span class="rate">76</span>%</div> <div class="self">购买本产品</div></div>';
    echo '</div></div>';
    echo '<div id="product_list_wrapper">';
    echo '<ul id="product_list">';
    $products = DbProduct::getList($this->category['table_prefix']);
    $count = 0;
    foreach (DbProduct::getList($this->category['table_prefix']) as $item) {
      if ($count === 4) {
        break;
      }
      ++$count;
      echo '<li class="item">';
      echo '<div class="rate_wrapper"><span class="rate">12</span>%</div>';
      echo '<div class="image"><a href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="';
      if ($count % 2 === 0) {
        echo 'x_large.jpg';
      } else {
        echo 'x.jpg';
      }
      echo '" /></a></div>';
      echo '<div class="title">';
      echo '<a href="/'.$item['id'].'">'.$item['name'].'</a></div><div class="data">';
      echo '<div>&yen;<span class="price">10000</span> ~ <span class="price">12299.84</span></div>';
      echo '<div>7 个商城</div>';
      echo '</div></li>';
    }
    echo '</ul>';
    echo '</div>';
  }

  private function renderAds($isImage = false) {
    echo '<div class="ads"><div>';
    AdSenseScreen::render($isImage);
    echo '</div></div>';
  }
}