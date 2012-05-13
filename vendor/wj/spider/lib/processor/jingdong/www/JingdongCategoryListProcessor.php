<?php
class JingdongCategoryListProcessor {
  public function execute($arguments) {
    $categoryId = DbId::get(
      'category', array('name' => $arguments['name'], 'parent_id' => 0)
    );
    $result = WebClient::get($arguments['domain'], $arguments['path']);
    $html = $result['content'];
    if ($html === false) {
      return $result;
    }
    preg_match_all(
      '{<li><a href=http://www.360buy.com/products/(.*?).html>(.*?)</a></li>}',
      $html,
      $matches
    );
    $count = count($matches[1]);
    for ($index = 1; $index < $count; ++$index) {
      Db::insert('task', array(
        'type' => 'ProductList', var_export(array(
          'path' => $matches[1][$index],
          'root_category_id' => $categoryId,
          'table_prefix' => $arguments['table_prefix'],
          'name' => iconv('gbk', 'utf-8', $matches[2][$index]),
          'page' => 1
        ), true)
      ));
    }
  }
}