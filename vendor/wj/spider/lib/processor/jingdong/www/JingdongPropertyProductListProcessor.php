<?php
class JingdongPropertyProductListProcessor {
  private $html;
  private $page;
  private $tablePrefix;
  private $valueId;

  public function execute($tablePrefix, $valueId, $path, $page = 1) {
    $result = WebClient::get(
      'www.360buy.com', '/products/'.$path.'.html'
    );
    if ($result['content'] === false) {
      return $result;
    }
    $this->html = $result['content'];
    $this->valueId = $valueId;
    $this->tablePrefix = $tablePrefix;
    $this->page = $page;
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
    $merchantProductIdList = $matches[1];
    foreach ($merchantProductIdList as $merchantProductId) {
      $productId = Db::getColumn(
        'SELECT id FROM '
          .$this->tablePrefix.'-product WHERE merchant_product_id = ?',
        $merchantProductId
      );
      if ($productId === false) {
        return;
      }
      Db::execute(
        'REPLACE INTO '.$this->tablePrefix.'-product-property_value'
          .'(product_id, property_value_id, is_updated) VALUES(?, ?, 1)',
        $productId, $this->valueId
      );
    }
  }

  private function parseNextPage() {
    preg_match(
      '{class="current".*?href="([0-9-]+).html"}', $this->html, $matches
    );
    if (count($matches) > 0) {
      $page = $this->page + 1;
      $path = $matches[1];
      Db::insert('task',  array('processor' => 'JingdongPropertyProductList',
        'argument_list' => var_export(array(
          $this->tablePrefix, $this->valueId, $path, $page
        ), true)
      ));
    }
  }
}