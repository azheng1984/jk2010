<?php
class JingdongCategoryBuilder {
  private $categoryId;

  public function execute($id) {
    $this->id = $id;
    $this->executeHistory();
    $this->checkProduct();
    $this->upgradeCategoryVersion();
  }

  private function upgradeCategoryVersion() {
    Db::update(
      'category',
      array('version' => $GLOBALS['VERSION']),
      'id = ?',
      $this->categoryId
    );
  }

  private function executeHistory() {
    $historyList = Db::getAll(
      'SELECT * FROM history WHERE category_id = ? AND version != ?',
      $this->categoryId, $GLOBALS['SPIDER_VERSION']
    );
    foreach ($historyList as $history) {
      $class = 'Jingdong'.$history['processor'].'Processor';
      $processor = new $class;
      $processor->execute($history['path']);
    }
  }

  private function checkProduct() {
    //todo:build/check property list
    $productList = Db::getAll(
      "SELECT id FROM product WHERE category_id = ? AND _status != 'ok'",
      $this->categoryId
    );
    //connect spider db
    foreach ($productList as $product) {
      Db::insert(
        'product_task',
        array('merchant_name' => 'jingdong','product_id' => $product['id'])
      );
    }
    //resume jingdong db
  }
}