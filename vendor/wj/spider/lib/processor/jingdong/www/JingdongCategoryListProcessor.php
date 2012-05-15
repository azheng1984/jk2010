<?php
class JingdongCategoryListProcessor {
  public function execute($table_prefix, $name, $domain, $path) {
    $result = WebClient::get($domain, $path);
    $html = $result['content'];
    preg_match_all(
      '{<li><a href=http://www.360buy.com/products/(.*?).html>(.*?)</a></li>}',
      $html,
      $matches
    );
    $count = count($matches[1]);
    for ($index = 1; $index < $count; ++$index) {
      $categoryId = DbId::get('category', array(
        'name' => iconv('gbk', 'utf-8', $matches[2][$index])
      ));
      $path = $matches[1][$index];
      Db::insert('task', array(
        'processor' => 'JingdongProductList',
        'argument_list' => var_export(array(
          $table_prefix, $categoryId, $path
        ), true)
      ));
    }
  }
}