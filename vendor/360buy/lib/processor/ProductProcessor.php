<?php
class ProductListProcessor {
  public function execute($arguments) {
    $product = new Product;
    $client = new WebClient;
    $result = $client->get(
      'www.360buy.com', '/product/'.$arguments['path'].'.html'
    );
    $product->insert(
      $arguments['id'], $arguments['category_id'], $result['content']
    );
  }
}