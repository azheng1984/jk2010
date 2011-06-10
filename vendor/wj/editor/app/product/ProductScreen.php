<?php
class ProductScreen extends Screen {
  public function renderContent() {
    echo '[<a href="/product/edit?category_id='.$_GET['category_id'].'&id='.$_GET['id'].'">编辑</a>] ';
    $category = Category::getById($_GET['category_id']);
    $product = new Product(Product::getById($category, $_GET['id']));
    echo '<h1>'.$product->getTitle().'</h1>';
    echo '<h2>描述</h2>';
    echo $product->getContent($category);
  }
}