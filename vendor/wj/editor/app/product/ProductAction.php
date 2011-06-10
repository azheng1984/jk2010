<?php
class ProductAction {
  public function GET() {}

  public function POST() {
    $category = Category::getById($_GET['category_id']);
    $product = new Product(Product::getById($category, $_GET['id']));
    $product->update($category->getTablePrefix(), $_GET['id'], $_POST['name']);
  }
}