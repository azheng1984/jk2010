<?php
class CategoryListProcessor {
  public function execute($arguments) {
    $categoryId = DbCategory::getOrNewId(
      $arguments['name'], $arguments['parent_category_id']
    );
    $result = WebClient::get($arguments['domain'], $arguments['path']);
    if (($html = $result['content']) === false) {
      return $result;
    }
    $pattern = '{<div class="search_category_panel">([\s\S]+)'
      .'<div class="search_category_bottom">}U';
    if (preg_match($pattern, $html, $match) !== 1) {
      return $result;
    }
    preg_match_all(
      '{<li><a href="http://category.dangdang.com/'
        .'list?cat=(.*?)" title="(.*?)">}',
      $match[1],
      $matches,
      PREG_SET_ORDER
    );
    if (count($matches) === 0) {
      DbTask::add('ProductList', array(
        'category_id' => $categoryId,
        'page' => 1
      ));
      return;
    }
    foreach ($matches as $match) {
      DbTask::add('CategoryList', array(
        'category_id' => $match[1],
        'name' => $match[2],
        'parent_category_id' => $categoryId,
      ));
    }
  }
}