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
    $breadcrumb = new Breadcrumb;
    $categories = array($this->category);
    $category = $this->category;
    while ($category['parent_id'] !== '0') {
      $category = DbCategory::get($category['parent_id']);
      array_unshift($categories, $category);
    }
    $breadcrumb->render($categories, $this->product);
    echo '<div id="product">';
    echo '<h1>'.$this->product['name'].'</h1>';
    echo '<div id="action"><a href="/">比较</a> <a href="/">关注</a> 12134 <a href="/">分享</a></div>';
    echo '<div id="property_list">';
    echo '<div class="product_image_box"><img title="'.$this->product['name'].'" class="product_image" src="/x_large.jpg" /></div>';
    echo '<div class="brand">品牌: <a href="/">DELL</a></div>';
    echo '</div>';
    echo '<div id="merchant_list">';
    echo '<table><thead><tr><th>商城</th><th>所在区域 不限 <a href="/">选择</a></th><th>配送范围 上海 <a href="/">选择</a></th><th><a href="/">价格</a></th><th><a href="/">运费</a></th><th><a href="/">总价</a></th></tr></thead>';
    echo '<tbody><tr><td><a href="/">京东商城</a></td><td>全国</td><td>全国</td><td>￥10.00</td><td>免运费</td><td>￥10.00</td></tr>';
    echo '<tr><td><a href="/">卓越亚马逊</a></td><td>北京 广州 上海</td><td>全国</td><td>￥9.00</td><td>￥5.00</td><td>￥14.00</td></tr>';
    echo '</tbody></table>';
    $this->renderProductList('笔记本电脑推荐');
    echo '</div>';
    echo '</div>';
  }

  private function renderProductList($name) {
    echo '<div class="featured_name">'.$name.'</div>';
    echo '<ul class="featured_product_list">';
    foreach (DbProduct::getList($this->category['table_prefix']) as $item) {
      echo '<li class="item"><div class="image"><a href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><h2><a href="/'.$item['id'].'">'
        .$item['name'].'</a></h2><div class="price_block">￥<span class="price">10000.00</span>~<span class="price">12299.00</span> 7个商城</div></li>';
    }
    echo '</ul>';
  }
}