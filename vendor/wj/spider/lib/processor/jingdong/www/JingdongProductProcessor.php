<?php
class JingdongProductProcessor {
  private $tablePrefix;
  private $merchantProductId;
  private $categoryId;
  private $saleRank;
  private $html;

  public function execute(
    $tablePrefix, $categoryId, $merchantProductId, $saleIndex
  ) {
    $result = WebClient::get(
      'www.360buy.com', '/product/'.$merchantProductId.'.html'
    );
    $this->tablePrefix = $tablePrefix;
    $this->merchantProductId = $merchantProductId;
    $this->saleRank = 1000000000 - $saleIndex;
    $this->categoryId = $categoryId;
    $this->html = $result['content'];
    $this->save();
    $this->insertImageTask();
    $this->insertPriceTask();
  }

  private function save() {
    $product = Db::getRow(
      'SELECT * FROM '.$this->tablePrefix
        .'-product WHERE merchant_product_id = ?',
      $this->merchantProductId
    );
    $uri = 'www.360buy.com/product/'.$this->merchantProductId.'.html';
    $title = $this->getTitle();
    if ($product === false) {
      Db::insert($this->tablePrefix.'-product', array(
        $this->merchantProductId,
        $uri,
        $this->categoryId,
        $title,
        $this->saleRank
      ));
      return;
    }
    $columnList = array('is_updated' => 1);
    if ($product['category_id'] !== $this->categoryId) {
      $columnList['category_id'] = $this->categoryId;
      Db::insert($this->tablePrefix.'-log', array(
        'product_id' => $product['id'], 'type' => 'CATEGORY'
      ));
    }
    if ($product['title'] !== $title) {
      $columnList['title'] = $title;
      Db::insert($this->tablePrefix.'-log', array(
        'product_id' => $product['id'], 'type' => 'TITLE'
      ));
    }
    if ($product['sale_rank'] !== $this->saleRank) {
      $columnList['sale_rank'] = $this->saleRank;
      Db::insert($this->tablePrefix.'-log', array(
        'product_id' => $product['id'], '`type`' => 'SALE_RANK'
      ));
    }
    Db::update(
      $this->tablePrefix.'-product', $columnList, 'id = ?', $this->productId
    );
  }

  private function getTitle() {
    preg_match(
    '{<h1>(.*?)<font}', $this->html, $matches
    );
    return trim(iconv('GBK', 'utf-8', $matches[1]));
  }

  private function insertImageTask() {
    preg_match(
      '{jqzoom.*? src="http://(.*?)/(\S+)"}', $this->html, $matches
    );
    if (count($matches) !== 3) {
      throw Exception;
    }
    $domain = $matches[1];
    $path = $matches[2];
    Db::insert('task', array('processor' => 'Image',
      'argument_list' => var_export(array(
        $this->tablePrefix,
        $this->productId,
        $this->merchantProductId,
        $this->categoryId,
        $domain,
        $path,
      ), true)
    ));
  }

  private function insertPriceTask() {
    Db::insert('task', array('processor' => 'JingdongPrice',
      'argument_list' => var_export(array(
        $this->tablePrefix,
        $this->productId,
        $this->merchantProductId
      ), true)
    ));
  }
}