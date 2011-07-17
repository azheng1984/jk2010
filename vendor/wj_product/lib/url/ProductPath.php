<?php
class ProductPath {
  public static function initialize($productId) {
    $sections = explode('/', $_SERVER['REQUEST_URI'], 2);
    $_GET['product_id'] = $sections[1];
  }
}