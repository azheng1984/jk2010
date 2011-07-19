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
    array_unshift($array, $var);
    $breadcrumb->render($categories, $this->product);
    echo '<div id="product">';
    print_r($this->product);
    echo '</div>';
  }
}