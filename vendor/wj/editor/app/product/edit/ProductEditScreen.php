<?php
class ProductEditScreen extends Screen {
  public function renderContent() {
    echo '[<a href="/product/edit?category='.$_GET['category_id'].'&id='.$_GET['id'].'">编辑</a>] ';
    $category = Category::getById($_GET['category_id']);
    $product = new Product(Product::getById($category, $_GET['id']));
    echo '<form method="POST" action="/product?category_id='.$_GET['category_id'].'&id='.$_GET['id'].'">';
    echo '标题: <input name="name" value="'.$product->getTitle().'" style="width:400px" />';
    echo '<h2>描述</h2>';
    echo $product->getEditContent($category);
    echo '<input type="submit" /></form>';
  }
}