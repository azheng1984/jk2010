<?php
class CategoryListProcessor {
  public function execute($arguments) {
    $categoryId = DbCategory::getOrNewId($arguments['name']);
    $result = WebClient::get($arguments['domain'], $arguments['path']);
    if (($html = $result['content']) === fasle) {
      return $result;
    }
    if(preg_match('{search_category_panel(.*?)</div>}', $html, $match) !== 1) {
      return $result;
    }
    preg_match_all(
      '{<a .*? href="(http://.*?/)?(.*?)" title="(.*?)">}',
      $match[1],
      $matches,
      PREG_SET_ORDER
    );
    foreach ($matches as $match) {
      DbTask::add('ProductList', array(
        'domain' => $arguments['domain'],
        'path' => '/'.$match[2],
        'parent_category_id' => $categoryId,
        'name' => iconv('gbk', 'utf-8', $match[3]),
        'page' => 1
      ));
    }
  }
}