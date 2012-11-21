<?php
class JingdongCategoryListProcessor {
  private $categoryId;
  private $categoryName;
  private $categoryVersion;

  public function execute() {
    $result = WebClient::get('www.360buy.com', '/allSort.aspx');
    preg_match_all(
      '{products/([0-9]+)-([0-9]+)-([0-9]+).html.>(.*?)<}',
      $result['content'],
      $matches
    );
    if (count($matches[0]) === 0) {
      throw new Exception(null, 500);
    }
    foreach ($matches[1] as $index => $levelOneCategoryId) {
      if ($matches[3][$index] === '000') {
        continue;
      }
      if ($levelOneCategoryId === '1713') {
        //publication
        continue;
      }
      if ($levelOneCategoryId === '5025') {
        //WatchProductList（brand as category）
        continue;
      }
      $this->categoryName = iconv('gbk', 'utf-8', $matches[4][$index]);
      $this->checkProductUpdateManagerTask();
      $this->bindCategory();
      $path = $levelOneCategoryId.'-'
        .$matches[2][$index].'-'.$matches[3][$index];
      if ($this->categoryVersion !== $GLOBALS['VERSION']) {
        $productListProcessor = new JingdongProductListProcessor;
        $productListProcessor->execute($path);
        $this->executeHistory();
        $this->cleanProduct();
        $this->cleanProductPropertyValue();
        $this->addProductUpdateManagerTask();
      }
      $this->upgradeCategoryVersion();
    }
    $categoryList = Db::getAll(
      'SELECT * FROM category WHERE version != ?', $GLOBALS['version']
    );
    foreach ($categoryList as $category) {
      $this->categoryId = $category['id'];
      $this->categoryName = $category['name'];
      $this->checkProductUpdateManagerTask();
      $hasHistory = $this->executeHistory();
      $this->cleanProduct();
      if ($hasHistory && $category['version'] < ($GLOBALS['VERSION'] - 1)) {
        ImageDb::deleteDb($category['name']);
        Db::delete('category', 'id = ?', $category['id']);
        continue;
      }
      if ($hasHistory === false) {
        continue;
      }
      $this->upgradeCategoryVersion();
      $this->cleanProductPropertyValue();
      $this->addProductUpdateManagerTask();
    }
    $this->addProductUpdateManagerTask(' LAST');
  }

  private function bindCategory() {
    $category = Db::getRow(
      'SELECT * FROM category WHERE name = ?', $this->categoryName
    );
    if ($category === false) {
      Db::insert('category', array('name' => $this->categoryName));
      $this->categoryId = Db::getLastInsertId();
      $this->categoryVersion = 0;
      return;
    }
    $this->categoryId = $category['id'];
    $this->categoryVersion = intval($category['version']);
  }

  private function checkProductUpdateManagerTask() {
    DbConnection::connect('update_manager');
    for (;;) {
      $id = Db::getColumn(
       'SELECT id FROM task WHERE merchant_name = ? AND category_name = ?',
       'jingdong', $this->categoryName
      );
      if ($id === false) {
        break;
      }
      sleep(10);
    }
    DbConnection::close();
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
    $historyIdList = Db::getAll(
      'SELECT id FROM history WHERE category_id = ? AND version != ?',
      $this->categoryId, $GLOBALS['VERSION']
    );
    if (count($historyIdList) === 0) {
      return false;
    }
    foreach ($historyIdList as $historyId) {
      $history = Db::getRow(
        'SELECT processor, path, version FROM history WHERE id = ?', $historyId
      );
      if ($history['version'] === $GLOBALS['VERSION']) {
        continue;
      }
      $class = 'Jingdong'.$history['processor'].'Processor';
      $processor = new $class;
      $processor->execute($history['path']);
    }
    return true;
  }

  private function addProductUpdateManagerTask() {
    DbConnection::connect('update_manager');
    Db::insert('task', array(
      'merchant_name' => 'jingdong',
      'category_name' => $this->categoryName,
      'version' => $GLOBALS['VERSION'],
    ));
    DbConnection::close();
  }

  private function cleanProduct() {
    $productIdList = Db::getAll(
      'SELECT id FROM product WHERE category_id = ? AND version < ?',
      $this->categoryId, $GLOBALS['VERSION'] - 1
    );
    foreach ($productIdList as $productId) {
      ImageDb::delete($this->categoryName, $productId);
    }
    Db::delete(
      'product', 'category_id = ? AND version < ?',
      $this->categoryId, $GLOBALS['VERSION'] - 1
    );
  }

  private function cleanProductPropertyValue() {
    Db::execute(
      'DELETE FROM product_property_value'
        .' WHERE category_id = ? AND version != ?',
      $this->categoryId, $GLOBALS['VERSION']
    );
  }
}