<?php
class CategoryListProcessor {
  public function execute($arguments) {
    $categoryId = DbCategory::getOrNewId($arguments['name']);
    $result = WebClient::get($arguments['domain'], $arguments['path']);
    $html = $result['content'];
    if ($html === false) {
      return $result;
    }
    $matches = array();
    preg_match_all(
      '{<li><a href=http://www.360buy.com/products/(.*?).html>(.*?)</a></li>}',
      $html,
      $matches
    );
    $count = count($matches[1]);
    for ($index = 1; $index < $count; ++$index) {
      DbTask::insert('ProductList', array(
        'path' => $matches[1][$index],
        'root_category_id' => $categoryId,
        'name' => iconv('gbk', 'utf-8', $matches[2][$index]),
        'page' => 1
      ));
    }
  }
}