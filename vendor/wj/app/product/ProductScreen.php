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
    echo '<h1>'.$this->product['name'].'</h1>';
    echo '<div id="action"><img src="/icon_ranking.gif" /> <a href="/">122</a> <a href="/">对比</a> <a href="/">关注</a> 12134 <a href="/">分享</a></div>';
    echo '<div id="property_list">';
    echo '<div class="product_image_box"><img title="'.$this->product['name'].'" class="product_image" src="/x.jpg" /></div>';
    $categoryPath = $this->getCategoryPath($categories);
    foreach (explode(',', $this->product['property_value_list']) as $id) {
      $result = DbProperty::getByValueId($this->category['table_prefix'], $id);
      echo '<div>'.$result['key'].': <a href="'.$categoryPath.'?'.urlencode($result['key']).'='.urlencode($result['value']).'">'.$result['value'].'</a></div>';
    }
    echo '</div>';
    echo '<div id="merchant_list">';
    echo '<table><thead><tr><th>商城</th><th>价格</th></tr></thead>';
    echo '<tbody><tr><td><a href="/">京东商城 <img class="merchant_logo" src="/360buy.com.2.gif" /></a></td><td>￥<a href="/" class="price">10.00</a></td></tr>';
    echo '<tr><td><a href="/">新蛋 <img class="merchant_logo" src="/newegg.com.cn.2.gif" /></a> </td><td>￥<a href="/" class="price">14.00</a></td></tr>';
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
        .$item['name'].'</a></h2><div class="price_block">￥<span class="price">10000.00</span>~<span class="price">12299.00</span> <div>7个商城</div></div></li>';
    }
    echo '</ul>';
  }
}