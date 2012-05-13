<?php
class DangdangProductProcessor {
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
      '{__bigpic_.*?"><img src="http://(.*?)/(.*?)"}',
      $result['content'],
      $match
    ) !== 1) {
      return $result;
    }
    DbTask::insert('Image', array(
      'id' => $arguments['id'],
      'category_id' => $arguments['category_id'],
      'domain' => $match[1],
      'path' => $match[2],
    ));
  }
}