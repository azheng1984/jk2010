<?php
//TODO 删除 category name 中的 "其它" 前缀
class JingdongCategoryListProcessor {
  private $categoryId;
  private $categoryName;
  private $categoryVersion;

  public function execute() {
    $result = WebClient::get('www.360buy.com', '/allSort.aspx');
    $matches = $this->parseCategory(
      'www.360buy.com/allSort.aspx', $result['content']
    );
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
      $this->bindCategory();
      $path = $levelOneCategoryId.'-'
        .$matches[2][$index].'-'.$matches[3][$index];
      if ($this->categoryVersion !== $GLOBALS['VERSION']) {
        $this->checkProductUpdateManagerTask();
        $productListProcessor = new JingdongProductListProcessor(
          $this->categoryId
        );
        $productListProcessor->execute($path);
        $this->executeHistory();
        $this->cleanProduct();
        $this->cleanProductPropertyValue();
        $this->addProductUpdateManagerTask();
      }
      $this->upgradeCategoryVersion();
    }
    $categoryList = Db::getAll(
      'SELECT * FROM category WHERE version != ?', $GLOBALS['VERSION']
    );
    foreach ($categoryList as $category) {
      $this->categoryId = $category['id'];
      $this->categoryName = $category['name'];
      $this->checkProductUpdateManagerTask();
      $hasHistory = $this->executeHistory();
      $this->cleanProduct();
      if ($hasHistory && $category['version'] < ($GLOBALS['VERSION'] - 1)) {
        ImageDb::deleteTable($category['id']);
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
    $this->categoryName = '<LAST>';
    DbConnection::connect('update_manager');
    $id = Db::getColumn(
      'SELECT id FROM task WHERE merchant_name = ? AND category_name = ?',
      'jingdong', $this->categoryName
    );
    if ($id === false) {
      $this->addProductUpdateManagerTask();
    }
    DbConnection::close();
  }

  private function parseCategory($url, $html) {
    preg_match_all(
      '{products/([0-9]+)-([0-9]+)-([0-9]+).html.>(.*?)<}',
      $html,
      $matches
    );
    if (count($matches[0]) === 0) {
      Db::insert('match_error_log', array(
        'source' => 'JingdongCategoryListProcessor:parseCategory',
        'url' => $url,
        'document' => $html,
        'time' => date('Y-m-d H:i:s'),
        'version' => $GLOBALS['VERSION']
      ));
      throw new Exception(null, 500);
    }
    return $matches;
  }

  private function bindCategory() {
    $category = Db::getRow(
      'SELECT * FROM category WHERE name = ?', $this->categoryName
    );
    if ($category === false) {
      Db::insert('category', array('name' => $this->categoryName));
      $this->categoryId = Db::getLastInsertId();
      ImageDb::createTable($this->categoryId);
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
      echo 'waiting for update manager...'.PHP_EOL;
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
    $historyList = Db::getAll(
      'SELECT * FROM history WHERE category_id = ? AND version != ?'
        .' ORDER BY id LIMIT 10000',
      $this->categoryId, $GLOBALS['VERSION']
    );
    if (count($historyList) === 0) {
      return false;
    }
    $this->executeHistoryList($historyList);
    while (count($historyList) === 10000) {
      $history = end($historyList);
      $historyList = Db::getAll(
          'SELECT * FROM history WHERE category_id = ? AND version != ?'
          .' AND id > ? ORDER BY id LIMIT 10000',
          $this->categoryId, $GLOBALS['VERSION'], $history['id']
      );
      $this->executeHistoryList($historyList);
    }
    return true;
  }

  private function executeHistoryList($historyList) {
    foreach ($historyList as $history) {
      if ($history['version'] === $GLOBALS['VERSION']) {
        continue;
      }
      $class = 'Jingdong'.$history['processor'].'Processor';
      $processor = new $class;
      $processor->execute($history['path']);
    }
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
      ImageDb::delete($this->categoryId, $productId);
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