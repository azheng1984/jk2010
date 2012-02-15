<?php
class SearchJson extends Json {
  private $list = array();

  public function __construct() {
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
      throw new NotFoundException;
    }
    $GLOBALS['KEY'] =
      DbPropertyKey::getByName($GLOBALS['CATEGORY']['id'], $_GET['key']);
    if ($GLOBALS['KEY'] === false) {
      return;
    }
    $this->buildValueList();
  }

  private function buildCategoryList() {
    $result = CategorySearchService::search();
    if ($result === false || isset($result['matches']) === false) {
      return;
    }
  }

  private function buildKeyList() {
    $result = KeySearchService::search();
    if ($result === false || isset($result['matches']) === false) {
      return;
    }
  }

  private function buildValueList() {
    $result = ValueSearchService::search();
    if ($result === false || isset($result['matches']) === false) {
      return;
    }
  }

  protected function renderJson() {
    echo '[]';
  }
}