<?php
class PublicationProductProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      $arguments['domain'].'.360buy.com', '/'.$arguments['id'].'.html'
    );
    if ($result['content'] === false) {
      return $result;
    }
    Db::insert($arguments['table_prefix'].'_product', array(
      'id' => $arguments['id'],
      'category-id' => $arguments['category_id'],
      'content' => $result['content']
    ));
    preg_match(
      '{src="http://(.*?)/(\S+)" width="280" height="280"}',
      $result['content'],
      $matches
    );
    if (count($matches) !== 3) {
      return $result;
    }
    Db::insert('task', array('type' => 'Image', 'argument_list' =>
      var_export(array(
        'id' => $arguments['id'],
        'category_id' => $arguments['category_id'],
        'domain' => $matches[1],
        'path' => $matches[2],
      ), true)
    ));
    Db::insert('task', array('type' => 'Price', 'argument_list' =>
      var_export(array('id' => $arguments['id']), true)
    ));
  }
}