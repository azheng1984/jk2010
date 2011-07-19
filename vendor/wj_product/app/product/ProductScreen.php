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
    echo '<div id="property_list">';
    echo '<img title="'.$this->product['name'].'" class="product_image" src="/x.jpg" />';
    echo '<div class="brand">品牌: <a href="/">DELL</a></div>';
    echo '</div>';
    echo '<div id="merchant_list">';
    echo '<table><thead><tr><th>商城</th><th>配送范围 上海 <a href="/">选择</a></th><th><a href="/">价格</a></th></tr></thead>';
    echo '<tbody><tr><td><a href="/">京东商城</a></td><td>全国</td><td>￥10.00</td></tr>';
    echo '<tr><td><a href="/">卓越亚马逊</a></td><td>全国</td><td>￥9.00</td></tr>';
    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
  }
}