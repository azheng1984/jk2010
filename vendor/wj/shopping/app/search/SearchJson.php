<?php
class SearchJson extends Json {
  private $list = array();

  public function __construct() {
    usleep(400000);
    if ($GLOBALS['PAGE'] > 5) {
      throw new NotFoundException;
    }
    if (isset($GLOBALS['CATEGORY']) === false) {
      $this->buildCategoryList();
      return;
    }
    if (isset($_GET['key']) === false) {
      $this->buildKeyList();
      return;
    }
    if (isset($GLOBALS['CATEGORY']['id']) === false) {
      return;
    }
    $GLOBALS['KEY'] =
      DbPropertyKey::getByName($GLOBALS['CATEGORY']['id'], $_GET['key']);
    if ($GLOBALS['KEY'] === false) {
      return;
    }
    $this->buildValueList();
  }

  protected function renderJson() {
    echo '[', implode(',', $this->list), ']';
  }

  private function buildCategoryList() {
    $result = CategorySearchService::search();
    if ($result === false) {
      return;
    }
    $this->list[] = $result['total_found'];
    foreach ($result['matches'] as $match) {
      $category = DbCategory::get($match['attrs']['@groupby']);
      if ($category !== false) {
        $this->list[] =
          '["'.$category['name'].'",'.$match['attrs']['@count'].']';
      }
    }
  }

  private function buildKeyList() {
    $result = KeySearchService::search();
    if ($result === false) {
      return;
    }
    $this->list[] = $result['total_found'];
    foreach ($result['matches'] as $match) {
      $key = DbPropertyKey::get($match['attrs']['@groupby']);
      if ($key !== false) {
        $this->list[] = '["'.$key['name'].'",'.$key['is_multiple'].']';
      }
    }
  }

  private function buildValueList() {
    $result = ValueSearchService::search();
    if ($result === false) {
      return;
    }
    $this->list[] = $result['total_found'];
    foreach ($result['matches'] as $match) {
      $value = DbPropertyValue::get($match['attrs']['@groupby']);
      if ($value !== false) {
        $this->list[] = '["'.$value['name'].'",'.$match['attrs']['@count'].']';
      }
    }
  }
}