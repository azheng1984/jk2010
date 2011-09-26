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
    echo '<title>货比万家</title>';
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
    echo '<div id="title_wrapper">';
    echo '<h1>'.$this->product['name'].'</h1>';
    echo '<div id="action"><div class="right"><a href="/" class="box">对比</a> <a href="/">关注 <span class="note">12134</span></a> <a href="/">分享</a></div></div>';
    echo '</div>';
    echo '<div id="product">';
    $this->renderPropertyList($categories);
    $this->renderRight();
    echo '</div>';
  }

   private function renderRight() {
    echo '<div id="right_wrapper">';
    echo '<div id="sort_wrapper_wrapper"><div id="sort_wrapper"><div class="sort">排序: <span class="selected">销量</span> <a href="/">价格</a></div></div></div>';
    $this->renderMerchantList();
    $this->renderAds();
    $this->renderProductList('笔记本电脑推荐');
    $this->renderAds();
    echo '</div>';
   }

  private function renderMerchantList() {
    echo '<div id="merchant_list">';
    
    echo '<div class="merchant top">';
    
    echo '<div class="merchant_info">';
    echo '<div class="logo"><a href="/"  target="_blank"><img alt="京东商城" class="merchant_logo" src="/360buy.com.2.gif" /> </a></div>';
    echo '<div><a href="/" target="_blank"><span class="merchant_name">京东商城</span></a></div>';
    echo '</div>';
    
    echo '<div class="promotions">';
    echo '<div class="price_column"><span class="price_sign">&yen;</span><span class="price">10.23</span></div>';
    echo '</div>';

    echo '</div>';

    echo '<div class="merchant last">';

    echo '<div class="merchant_info">';
    echo '<div class="logo"><a href="/" target="_blank"><img alt="新蛋" class="merchant_logo" src="/newegg.com.cn.2.gif" /></a></div>';
    echo '<div><a href="/" target="_blank"><span class="merchant_name">新蛋网</span></a></div>';
    echo '</div>';

    echo '<div class="promotions">';
    echo '<div class="price_column"><span class="price_sign">&yen;</span><span class="price">14</span></a></div>';
    echo '</div>';

    echo '</div>';
    
    echo '</div>';
  }

  private function renderPropertyList($categories) {
    echo '<div id="property_list_wrapper">';
    echo '<div class="product_image_box"><img title="'.$this->product['name'].'" class="product_image" src="/x.jpg" /></div>';
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

  private function renderProductList($name) {
    echo '<div class="featured_name"><div>'.$name.'</div></div>';
    echo '<div id="featured_product_list_wrapper">';
    echo '<ul class="featured_product_list">';
    foreach (DbProduct::getList($this->category['table_prefix']) as $item) {
      echo '<li class="item"><div class="image"><a href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><h2><a href="/'.$item['id'].'">'
        .$item['name'].'</a></h2><div class="price_block"><span class="rmb">&yen;</span><span class="price">10000</span> &#8764; <span class="price">12299.84</span> <div>7 个商城</div></div></li>';
    }
    echo '</ul>';
    echo '</div>';
  }

  private function renderAds() {
    echo '<div class="featured_name"><div>Google 提供的广告</div></div>';
  }
}