<?php
class ProductProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      'product.dangdang.com', '/product.aspx?product_id='.$arguments['id']
    );
    if ($result['content'] === false) {
      return $result;
    }
    DbProduct::insert(
      $arguments['id'], $arguments['category_id'], $result['content']
    );
    if (preg_match(
      '{__bigpic_pub"><img src="http://(.*?)/(.*?)"}',
      $result['content'],
      $matches
    ) === false) {
      return $result;
    }
    DbTask::add('Image', array(
      'id' => $arguments['id'],
      'category_id' => $arguments['category_id'],
      'domain' => $matches[1],
      'path' => $matches[2],
    ));
  }
}