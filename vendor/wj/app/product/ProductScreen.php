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

  public function renderContent() {
    $categories = array($this->category);
    $category = $this->category;
    while ($category['parent_id'] !== '0') {
      $category = DbCategory::get($category['parent_id']);
      array_unshift($categories, $category);
    }
    $breadcrumb = new Breadcrumb($categories, $this->product);
    $breadcrumb->render();
    echo '<div id="product">';
    echo '<div id="action">[ <a href="/">笔记本电脑排名</a> #22 ] [ <a href="/">对比</a> ] [ <a href="/">关注</a> 12134 ] [ <a href="/">分享</a> ]</div>';
    echo '<h1>'.$this->product['name'].'</h1>';
    echo '<div id="property_list">';
    echo '<div class="product_image_box"><img title="'.$this->product['name'].'" class="product_image" src="/x.jpg" /></div>';
    $categoryPath = $this->getCategoryPath($categories);
    echo '<div>品牌: <a href="..">ThinkPad</a></strong></div>';
    echo '<div>型号: G470AH-ITH</div>';
    foreach (explode(',', $this->product['property_value_list']) as $id) {
      $result = DbProperty::getByValueId($this->category['table_prefix'], $id);
      echo '<div>'.$result['key'].': <a rel="nofollow" href="'.$categoryPath.'?'.urlencode($result['key']).'='.urlencode($result['value']).'">'.$result['value'].'</a></div>';
    }
    echo '</div>';
    echo '<div id="merchant_list">';
    echo '<table>';
    echo '<tbody><tr><td><a href="/"><img alt="京东商城" title="京东商城" class="merchant_logo" src="/360buy.com.2.gif" /></a><div class="merchant_name"></div></td><td class="price_column"><span class="price_block">￥</span><a href="/" class="price">10.23<br /><span class="merchant_name">京东商城</span></a></td></tr>';
    echo '<tr><td><a href="/"><img alt="新蛋" title="新蛋" class="merchant_logo" src="/newegg.com.cn.2.gif" /></a><div class="merchant_name"></div></td><td class="price_column"><span class="price_block">￥</span><a href="/" class="price">14<br /><span class="merchant_name">新蛋网</span></a></td></tr>';
    echo '</tbody></table>';
    $this->renderProductList('笔记本电脑推荐');
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
    echo '<div class="featured_name">'.$name.'</div>';
    echo '<ul class="featured_product_list">';
    foreach (DbProduct::getList($this->category['table_prefix']) as $item) {
      echo '<li class="item"><div class="image"><a href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><h2><a href="/'.$item['id'].'">'
        .$item['name'].'</a></h2><div class="price_block"><span class="rmb">￥</span><span class="price">10000</span> ~ <span class="price">12299.84</span> <div>7 个商城</div></div></li>';
    }
    echo '</ul>';
  }
}