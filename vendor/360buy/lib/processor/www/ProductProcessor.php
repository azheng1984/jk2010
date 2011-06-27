<?php
class ProductProcessor {
  public function execute($arguments) {
    $product = new Product;
    $client = new WebClient;
    $result = $client->get(
      'www.360buy.com', '/product/'.$arguments['id'].'.html'
    );
    $product->insert(
      $arguments['id'], $arguments['category_id'], $result['content']
    );
    $matches = array();
    preg_match(
      'jdzoom.*? src="http://(.*?)/(.*?)"', $result['content'], $matches
    );
    $this->task = new Task();
    $this->task->add('Image', array(
      'id' => $arguments['id'],
      'category_id' => $arguments['category_id'],
      'domain' => $matches[0][1],
      'path' => $matches[0][2],
    ));
    $this->task->add('Price', array('id' => $arguments['id']));
  }
}