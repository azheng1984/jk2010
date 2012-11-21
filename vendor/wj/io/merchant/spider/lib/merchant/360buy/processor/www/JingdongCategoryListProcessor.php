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
    $lastIndex = count($matches[1]) - 1;
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
        $isLast = false;
        if ($index === $lastIndex) {
          $isLast = true;
        }
        $this->addProductUpdateManagerTask($categoryName, $isLast);
      }
      $this->upgradeCategoryVersion();
    }
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
    $this->categoryVersion = $category['version'];
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

  private function addProductUpdateManagerTask($categoryName, $isLast) {
    DbConnection::connect('update_manager');
    Db::insert('task', array(
      'merchant_name' => 'jingdong',
      'category_name' => $categoryName,
      'version' => $GLOBALS['VERSION'],
      'is_last' => $isLast
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