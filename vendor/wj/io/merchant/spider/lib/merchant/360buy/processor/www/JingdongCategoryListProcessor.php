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
    //TODO:检查 update task 是否还没有被处理，如果没有处理就等待
    //TODO:删除上上版本的产品
    foreach ($matches[1] as $index => $levelOneCategoryId) {
      if ($matches[3][$index] === '000') {//leaf category only
        continue;
      }
      if ($levelOneCategoryId === '1713') {//publication
        continue;
      }
      if ($levelOneCategoryId === '5025') {
        //WatchProductList（brand as category）
        continue;
      }
      $categoryName = $matches[4][$index];
      $this->setCategory(iconv('gbk', 'utf-8', $categoryName));
      $path = $levelOneCategoryId.'-'
        .$matches[2][$index].'-'.$matches[3][$index];
      if ($this->categoryVersion !== $GLOBALS['VERSION']) {
        $productListProcessor = new JingdongProductListProcessor;
        $productListProcessor->execute($path);
        $this->executeHistory();
        $this->cleanProductPropertyValue();
        $this->cleanPropertyKey();
        $this->cleanPropertyValue();
        $this->addProductUpdateTask();
      }
      //TODO:update category version
    }
  }

  private function setCategory($name) {
    $category = Db::getRow(
      'SELECT * FROM catagory WHERE name = ?', $name
    );
    if ($category === false) {
      Db::insert('category', array('name' => $name));
      $this->categoryId = Db::getLastInsertId();
      $this->categoryVersion = 0;
      return;
    }
    $this->categoryId = $category['id'];
    $this->categoryVersion = $category['version'];
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

  private function addProductUpdateTask() {
    
  }

  private function cleanProductPropertyValue() {
    Db::execute(
      'DELETE FROM product_property_value'
        .' WHERE category_id = ? AND  version != ?',
      $this->categoryId, $GLOBALS['VERSION']
    );
  }

  private function cleanPropertyKey() {
    Db::execute(
      'DELETE FROM property_key WHERE category_id = ? AND version != ?',
      $this->categoryId, $GLOBALS['VERSION']
    );
  }

  private function cleanPropertyValue() {
    Db::execute(
      'DELETE FROM property_value WHERE category_id = ? AND  version != ?',
      $this->categoryId, $GLOBALS['VERSION']
    );
  }
}