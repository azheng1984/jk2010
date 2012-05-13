<?php
class JingdongPublicationCategoryListProcessor {
  public function execute($arguments) {
    $result = WebClient::get($arguments['domain'], $arguments['path']);
    $html = $result['content'];
    $categoryId = DbId::get(
      'category', array('name' => $arguments['name'], 'parent_id' => 0)
    );
    preg_match('{</h2>[\s\S]+<!--main end-->}', $html, $matches);
    $main = $matches[0];
    $sections = explode('</dl>', iconv('gbk', 'utf-8', $main));
    array_pop($sections);
    foreach ($sections as $section) {
      preg_match('{<dt>.*?>\s*(.*?)<}', $section, $matches);
      $sectionCategoryId = DbId::get(
        'category', array('name' => $matches[1], 'parent_id' => $categoryId)
      );
      preg_match_all(
        '{<em>.*?href=".*?products/(.*?).html">\s*(.*?)</a></em>}',
        $section,
        $matches
      );
      $amount = count($matches[0]);
      for ($index = 0; $index < $amount; ++$index) {
        Db::insert('task', array(
          'type' => 'PublicationProductList',
          'argument_list' => var_export(array(
            'path' => $matches[1][$index],
            'name' => $matches[2][$index],
            'parent_category_id' => $sectionCategoryId,
            'page' => 1
          ), true)
        ));
      }
    }
  }
}