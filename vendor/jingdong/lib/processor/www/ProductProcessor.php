<?php
class ProductProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/product/'.$arguments['id'].'.html'
    );
    if ($result['content'] === false) {
      return $result;
    }
    DbProduct::insert(
      $arguments['id'], $arguments['category_id'], $result['content']
    );
    $matches = array();
    preg_match(
      '{jqzoom.*? src="http://(.*?)/(\S+)"}', $result['content'], $matches
    );
    if (count($matches) !== 3) {
      return $result;
    }
    DbTask::insert('Image', array(
      'id' => $arguments['id'],
      'category_id' => $arguments['category_id'],
      'domain' => $matches[1],
      'path' => $matches[2],
    ));
    DbTask::insert('Price', array('id' => $arguments['id']));
  }
}