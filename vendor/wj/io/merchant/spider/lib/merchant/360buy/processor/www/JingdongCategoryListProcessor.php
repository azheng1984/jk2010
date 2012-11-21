<?php
class JingdongCategoryListProcessor {
  private $categoryId;
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
      $categoryName = iconv('gbk', 'utf-8', $matches[4][$index]);
      $this->checkProductUpdateManagerTask($categoryName);
      $this->bindCategory($categoryName);
      $path = $levelOneCategoryId.'-'
        .$matches[2][$index].'-'.$matches[3][$index];
      if ($this->categoryVersion !== $GLOBALS['VERSION']) {
        $productListProcessor = new JingdongProductListProcessor;
        $productListProcessor->execute($path);
        $this->executeHistory();
        $this->cleanProduct();
        $this->cleanProductPropertyValue();
        $this->addProductUpdateManagerTask($categoryName);
      }
      $this->upgradeCategoryVersion();
    }
    $categoryList = Db::getAll(
      'SELECT id FROM category WHERE version != ?', $GLOBALS['version']
    );
    foreach ($categoryList as $category) {
      $this->categoryId = $category['id'];
      $this->checkProductUpdateManagerTask($category['name']);
      $hasHistory = $this->executeHistory();
      $this->cleanProduct();
      if ($hasHistory && $category['version'] < ($GLOBALS['VERSION'] - 1)) {
        ImageDb::delete($category['id']);
        Db::delete('category', 'id = ?', $category['id']);
        continue;
      }
      if ($hasHistory === false) {
        continue;
      }
      $this->upgradeCategoryVersion();
      $this->cleanProductPropertyValue();
      $this->addProductUpdateManagerTask($categoryName);
    }
    $this->addProductUpdateManagerTask(' LAST');
  }

  private function bindCategory($name) {
    $category = Db::getRow(
      'SELECT * FROM category WHERE name = ?', $name
    );
    var_dump($category);
    if ($category === false) {
      Db::insert('category', array('name' => $name));
      $this->categoryId = Db::getLastInsertId();
      $this->categoryVersion = 0;
      return;
    }
    $this->categoryId = $category['id'];
    $this->categoryVersion = intval($category['version']);
  }

  private function checkProductUpdateManagerTask($categoryName) {
    DbConnection::connect('update_manager');
    for (;;) {
      $id = Db::getColumn(
       'SELECT id FROM task WHERE merchant_name = ? AND category_name = ?',
       'jingdong', $categoryName
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

  private function addProductUpdateManagerTask($categoryName, $isLast) {
    DbConnection::connect('update_manager');
    Db::insert('task', array(
      'merchant_name' => 'jingdong',
      'category_name' => $categoryName,
      'version' => $GLOBALS['VERSION'],
    ));
    DbConnection::close();
  }

  private function cleanProduct() {
    Db::execute(
      'DELETE FROM product WHERE category_id = ? AND version < ?',
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