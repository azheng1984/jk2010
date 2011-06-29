<?php
class PublicationProductProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      $arguments['domain'].'.360buy.com', '/'.$arguments['id'].'.html'
    );
    DbProduct::insert(
      $arguments['id'], $arguments['category_id'], $result['content']
    );
    $matches = array();
    preg_match(
      '{src="http://(.*?)/(\S+)" width="280" height="280"}',
      $result['content'],
      $matches
    );
    DbTask::add('Image', array(
      'id' => $arguments['id'],
      'category_id' => $arguments['category_id'],
      'domain' => $matches[1],
      'path' => $matches[2],
    ));
  }
}