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
    $matches = array();
    preg_match(
      'src="http://(.*?)/(.*?)" width="280" height="280"',
       $result['content'],
       $matches
    );
    $this->task = new Task();
    $this->task->add('Image', array(
      'id' => $arguments['id'],
      'category_id' => $arguments['category_id'],
      'domain' => $matches[0][1],
      'path' => $matches[0][2],
    ));
    exit;
  }
}