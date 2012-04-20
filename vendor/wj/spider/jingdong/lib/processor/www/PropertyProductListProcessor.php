<?php
class PropertyProductListProcessor {
  private $html;
  private $page;
  private $tablePrefix;
  private $valueId;

  public function execute($arguments) {
    $result = WebClient::get(
      'www.360buy.com', '/products/'.$arguments['path'].'.html'
    );
    if ($result['content'] === false) {
      return $result;
    }
    $this->html = $result['content'];
    $this->valueId = $arguments['value_id'];
    $this->tablePrefix = $arguments['table_prefix'];
    $this->page = $arguments['page'];
    $this->parseProductList();
    $this->parseNextPage();
  }

  private function parseProductList() {
    preg_match_all(
      "{<div class='p-name'><a target='_blank'"
        ." href='http://www.360buy.com/product/([0-9]+).html'>}",
      $this->html,
      $matches
    );
    $merchantProductIds = $matches[1];
    foreach ($merchantProductIds as $merchantProductId) {
      DbProductProperty::replace(
        $this->tablePrefix, $merchantProductId, $this->valueId
      );
    }
  }

  private function parseNextPage() {
    preg_match(
      '{class="current".*?href="([0-9-]+).html"}', $this->html, $matches
    );
    if (count($matches) > 0) {
      $page = $this->page + 1;
      DbTask::insert('PropertyProductList', array(
        'path' => $matches[1], 
        'value_id' => $this->valueId,
        'table_prefix' => $this->tablePrefix,
        'page' => $page
      ));
    }
  }
}