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
      $this->categoryId, $GLOBALS['VERSION']
    );
    foreach ($historyList as $history) {
      $class = 'Jingdong'.$history['processor'].'Processor';
      $processor = new $class;
      $processor->execute($history['path']);
    }
  }

  private function checkProduct() {
    //delete product
    //create/update product
    //直接操作本地镜像 shopping 数据库，减少中间层
    Db::update(
      'product',
      array('_status' => 'deleted'),
      'version != ?',
      $GLOBALS['version']
    );
    DbConnection::connect('spider');
    Db::execute(
      "REPLACE INTO product_builder_task(spider, category_id)"
        ." VALUES('jingdong', ?)",
      $this->categoryId
    );
    DbConnection::connect('jingdong');
  }
}