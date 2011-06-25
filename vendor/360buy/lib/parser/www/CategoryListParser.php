<?php
class CategoryListParser {
  public function execute($domain, $path, $name) {
    $client = new WebClient;
    $category = new Category;
    $categoryId = $category->getOrNewId($name);
    $result = $client->get($domain, $path);
    $html = $result['content'];
    $matches = array();
    preg_match_all(
      '{<li><a href=http://www.360buy.com/products/(.*?).html>(.*?)</a></li>}',
      $html,
      $matches
    );
    $count = count($matches[1]);
    $list = array();
    for ($index = 1; $index < $count; ++$index) {
      $list[iconv('gbk', 'utf-8', $matches[2][$index])] = $matches[1][$index];
    }
    return $list;
  }
}