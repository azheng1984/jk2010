<?php
class CategoryListProcessor {
  public function execute($arguments) {
    $client = new WebClient;
    $category = new Category;
    $categoryId = $category->getOrNewId($arguments['name']);
    $result = $client->get($arguments['domain'], $arguments['path']);
    $html = $result['content'];
    $matches = array();
    preg_match_all(
      '{<li><a href=http://www.360buy.com/products/(.*?).html>(.*?)</a></li>}',
      $html,
      $matches
    );
    $count = count($matches[1]);
    $task = new Task;
    for ($index = 1; $index < $count; ++$index) {
      $task->add('ProductList', array(
        'path' => $matches[1][$index],
        'root_category_id' => $categoryId,
        'name' => iconv('gbk', 'utf-8', $matches[2][$index]),
        'page' => 1
      ));
    }
  }
}