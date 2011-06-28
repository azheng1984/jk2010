<?php
class ProductProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/product/'.$arguments['id'].'.html'
    );
    DbProduct::insert(
      $arguments['id'], $arguments['category_id'], $result['content']
    );
    $matches = array();
    preg_match(
      'jdzoom.*? src="http://(.*?)/(.*?)"', $result['content'], $matches
    );
    DbTask::add('Image', array(
      'id' => $arguments['id'],
      'category_id' => $arguments['category_id'],
      'domain' => $matches[0][1],
      'path' => $matches[0][2],
    ));
    DbTask::add('Price', array('id' => $arguments['id']));
  }
}