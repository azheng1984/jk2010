<?php
class PublicationProductProcessor {
  public function execute($arguments) {
    $client = new WebClient;
    $result = $client->get(
      $arguments['domain'].'.360buy.com', $arguments['id'].'.html'
    );
    $product = new Product;
    $product->insert(
      $arguments['id'], $arguments['category_id'], $result['content']
    );
    $this->getImage();
    exit;
  }

  private function getImage() {
    
  }
}