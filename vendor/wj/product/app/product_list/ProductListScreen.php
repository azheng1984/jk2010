<?php
class ProductListScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    foreach (Product::getList() as $product) {
      echo '<div><a href="'.$product['path'].'/">'.$product['name'].'</a></div>';
    }
  }
}