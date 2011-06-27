<?php
class PublicationCategoryListProcessor {
  public function execute($arguments) {
    $client = new WebClient;
    $result = $client->get($arguments['domain'], $arguments['path']);
    $html = $result['content'];
    $category = new Category;
    $categoryId = $category->getOrNewId($arguments['name']);
    $task = new Task;
    $matches = array();
    preg_match('{</h2>[\s\S]+<!--main end-->}', $html, $matches);
    $main = $matches[0];
    $sections = explode('</dl>', iconv('gbk', 'utf-8', $main));
    array_pop($sections);
    foreach ($sections as $section) {
      preg_match('{<dt>.*?>\s*(.*?)<}', $section, $matches);
      $sectionCategoryId = $category->getOrNewId($matches[1], $categoryId);
      preg_match_all(
        '{<em>.*?href=".*?products/(.*?).html">\s*(.*?)</a></em>}',
        $section,
        $matches
      );
      $amount = count($matches[0]);
      for ($index = 0; $index < $amount; ++$index){
        $task->add('PublicationProductList', array(
          'path' => $matches[1][$index],
          'name' => $matches[2][$index],
          'parent_category_id' => $sectionCategoryId,
          'page' => 1
        ));
      }
    }
  }
}