<?php
class DangdangCategoryListProcessor {
  public function execute($arguments) {
    $categoryId = DbCategory::getOrNewId(
      $arguments['name'], $arguments['parent_category_id']
    );
    $result = WebClient::get('category.dangdang.com', $arguments['path']);
    if (($html = $result['content']) === false) {
      return $result;
    }
    $pattern = '{<div class="search_category_panel">([\s\S]+)'
      .'<div class="search_category_bottom">}U';
    if (preg_match($pattern, $html, $match) !== 1) {
      return $result;
    }
    preg_match_all(
      '{<li><a href="http://category.dangdang.com'
        .'(/list\?cat=.*?)" title="(.*?)">}',
      $match[1],
      $matches,
      PREG_SET_ORDER
    );
    if (count($matches) === 0) {
      DbTask::insert('ProductList', array(
        'path' => $arguments['path'],
        'category_id' => $categoryId,
        'page' => 1
      ));
    }
    foreach ($matches as $match) {
      DbTask::insert('CategoryList', array(
        'path' => $match[1],
        'category_id' => $categoryId,
        'name' => iconv('gbk', 'utf-8', $match[2]),
        'parent_category_id' => $categoryId,
      ));
    }
  }
}