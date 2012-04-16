<?php
class PublicationProductListProcessor {
  private $html;
  private $page;
  private $categoryId;

  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/products/'.$arguments['path'].'.html'
    );
    $this->page = $arguments['page'];
    $this->html = $result['content'];
    if ($this->html === false) {
      return $result;
    }
    $this->categoryId = $this->getCategoryId($arguments);
    $this->parseProductList();
    $this->parseNextPage();
  }

  private function getCategoryId($arguments) {
    if (isset($arguments['category_id'])) {
      return $arguments['category_id'];
    }
    return DbCategory::getOrNewId(
      $arguments['name'], $arguments['parent_category_id']
    );
  }

  private function parseProductList() {
    $matches = array();
    preg_match_all(
    "{<dt class=\"p-name\">\\s*<a target='_blank'"
      .' href="http://(.*?).360buy.com/(.*?).html">}',
      $this->html,
      $matches
    );
    $productIds = $matches[2];
    foreach ($productIds as $id) {
      DbTask::insert('PublicationProduct', array(
        'domain' => $matches[1][0],
        'category_id' => $this->categoryId,
        'id' => $id
      ));
    }
  }

  private function parseNextPage() {
    $matches = array();
    preg_match(
      '{class="current".*?href="([0-9-]+).html"}',
      $this->html,
      $matches
    );
    if (count($matches) > 0) {
      $page = $this->page + 1;
      DbTask::insert('PublicationProductList', array(
        'path' => $matches[1],
        'category_id' => $this->categoryId,
        'page' => $page
      ));
    }
  }
}