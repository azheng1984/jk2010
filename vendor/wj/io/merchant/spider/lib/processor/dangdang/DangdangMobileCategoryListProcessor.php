<?php
class DangdangMobileCategoryListProcessor {
  public function execute($arguments) {
    $categoryId = DbCategory::getOrNewId(
      $arguments['name'], $arguments['parent_category_id']
    );
    $result = WebClient::get($arguments['domain'], $arguments['path']);
    if (($html = $result['content']) === false) {
      return $result;
    }
    if (
      preg_match('{<div class="apmd">([\s\S]+)</div>}U', $html, $match) !== 1
    ) {
      return $result;
    }
    if ($this->isLeafCategory($arguments['name'], $match[1])) {
      DbTask::insert('MobileProductList', array(
        'cid' => str_replace(
          '/category_list.php?cid=', '', $arguments['path']
        ),
        'category_id' => $categoryId,
        'page' => 1
      ));
      return;
    }
    preg_match_all(
      '{<a href="category.php\?cid=(.*?)&.*?">(.*?)</a>}',
      $match[1],
      $matches,
      PREG_SET_ORDER
    );
    foreach ($matches as $match) {
      DbTask::insert('MobileCategoryList', array(
        'domain' => $arguments['domain'],
        'path' => '/category_list.php?cid='.$match[1],
        'parent_category_id' => $categoryId,
        'name' => $match[2],
      ));
    }
  }

  private function isLeafCategory($name, $categoryListHtml) {
    return preg_match(
      '{'.$name.'\([1-9]+\)&nbsp;}', $categoryListHtml, $match
    ) === 1;
  }
}