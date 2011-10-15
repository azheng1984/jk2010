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
    $this->product['name'] = $this->product['brand'].' '.$this->product['model'].' '.$this->category['name'];
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
    echo '<div id="action"><a rel="nofollow" href="/">对比</a> <a rel="nofollow" href="/">关注 <span class="note">12134</span></a> <a rel="nofollow" href="/">分享</a></div>';
    echo '</div>';
    echo '<div id="product">';
    $this->renderPropertyList($categories);
    $this->renderRight();
    echo '</div>';
  }

   private function renderRight() {
    $merchants = DbMerchant::getList($this->product['id']);
    echo '<div id="right_wrapper">';
    echo '<div id="list"><div id="sort">排序: <span>销量</span> <a rel="nofollow" href="/">价格</a></div>';
    echo '<div id="total">找到 '.count($merchants).' 个商家</div>';
    echo '</div>';
    $this->renderMerchantList($merchants);
    $this->renderAds(true);
    $this->renderProductList();
    $this->renderAds(true);
    echo '</div>';
   }

  private function renderMerchantList($merchants) {
    echo '<ol id="merchant_list">';
    foreach ($merchants as $merchant) {
      echo '<li>';
      echo '<div class="description">';
      echo '<div class="logo"><a rel="nofollow" href="'.$merchant['url'].'" target="_blank">';
      echo '<img alt="'.$merchant['name'].'" src="/img/merchant/'.$merchant['domain'].'.gif" /></a></div>';
      echo '<div class="name"><a href="'.$merchant['url'].'" target="_blank" rel="nofollow">';
      echo $merchant['name'].'</a></div>';
      echo '</div>';
      echo '<div class="promotion">';
      echo '<span class="rmb">&yen;</span><span class="price">'.$merchant['price'].'</span>';
      echo '</div>';
      echo '</li>';
    }
    echo '</ol>';
  }

  private function renderPropertyList($categories) {
    echo '<div id="property_list_wrapper">';
    echo '<div id="image_wrapper"><img title="'.$this->product['name'].'" alt="'.$this->product['name'].'" src="http://img.workbench.wj.com/'.$this->product['id'].'.jpg" /></div>';
    $categoryPath = $this->getCategoryPath($categories);
    echo '<ul id="property_list">';
    $properties = array();
    $sortIndex = array();
    foreach (explode(',', $this->product['property_value_list']) as $id) {
      $result = DbProperty::getByValueId($this->category['table_prefix'], $id);
      if (!isset($properties[$result['key']])) {
        $properties[$result['key']] = array();
        $sortIndex[$result['key']] = $result['rank'];
      }
      $properties[$result['key']][] = $result['value'];
    }
    $properties['型号'] = array($this->product['model']);
    $sortIndex['型号'] = '91';
    arsort($sortIndex);
    foreach ($sortIndex as $key => $values) {
      echo '<li>'.$key.': ';
      $values = $properties[$key];
      if ($key === '型号') {
        echo $values[0].'</li>';
        continue;
      }
      $tmps = array();
      foreach ($values as $value) {
        $tmps[] = '<a rel="nofollow" href="'.$categoryPath.'?'.urlencode($key).'='.urlencode($value).'">'.$value.'</a>';
      }
      echo implode(', ', $tmps), '</li>';
    }
    echo '</ul>';
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
    echo '<div id="buy_rate"><div class="rate"><span>76</span>%</div> <div class="self">购买本产品</div></div>';
    echo '</div></div>';
    echo '<div id="product_list_wrapper">';
    echo '<ol id="product_list">';
    $products = DbProduct::getList($this->category['table_prefix']);
    $count = 0;
    foreach (DbProduct::getList($this->category['table_prefix']) as $item) {
      if ($count === 4) {
        break;
      }
      ++$count;
      $name = $item['brand'].' '.$item['model'].' '.$this->category['name'];
      echo '<li>';
      echo '<div class="rate"><span>12</span>%</div>';
      echo '<div class="image"><a href="/'.$item['id'].'"><img title="'.$name.'" alt="'.$name.'" src="'
        .'http://img.workbench.wj.com/'.$item['id'].'.jpg" /></a></div>';
      echo '<div class="title">';
      echo '<a href="/'.$item['id'].'">'.$name.'</a></div><div class="data">';
      echo '<div>&yen;<span class="price">'.$item['lowest_price'].'</span> ~ <span class="price">12299.84</span></div>';
      echo '<div>7 个商城</div>';
      echo '</div></li>';
    }
    echo '</ol>';
    $this->renderProductListPagination();
    echo '</div>';
  }

  private function renderProductListPagination() {
    echo '<div id="small_pagination"><span class="selected"></span><span></span><span></span><span></span><span></span><a href="javascript:void(0)">&raquo;</a></div>';
  }

  private function renderAds($isImage = false) {
    echo '<div class="ads"><div>';
    AdSenseScreen::render($isImage);
    echo '</div></div>';
  }
}