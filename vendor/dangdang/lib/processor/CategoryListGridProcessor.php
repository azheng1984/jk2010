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
    preg_match(
      '{search_category_panel(.*?)</div>}',
      $html,
      $matches
    );
    echo $arguments['path'];
    print_r($matches);
    exit;
    if (count($matches) !== 2) {
      return $result;
    }
    preg_match_all(
      '{<a .*? href="(http://.*?/)?(.*?)" title="(.*?)">}',
      $matches[1],
      $matches
    );
    print_r($matches);
    $count = count($matches[1]);
    for ($index = 1; $index < $count; ++$index) {
      DbTask::add('ProductList', array(
        'domain' => $arguments['domain'],
        'path' => '/'.$matches[2][$index],
        'parent_category_id' => $categoryId,
        'name' => iconv('gbk', 'utf-8', $matches[3][$index]),
        'page' => 1
      ));
    }
    exit;
  }
}