<?php
class JingdongCategoryListProcessor {
  private $categoryId;

  public function execute() {
    $result = WebClient::get('www.360buy.com', '/allSort.aspx');
    preg_match_all(
      '{products/([0-9]+)-([0-9]+)-([0-9]+).html}', $result['content'], $matches
    );
    if (count($matches[0]) === 0) {
      throw new Exception(null, 500);
    }
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
      $productListProcessor = new JingdongProductListProcessor;
      $productListProcessor->execute(
        $levelOneCategoryId.'-'.$matches[2][$index].'-'.$matches[3][$index]
      );
      $this->executeHistory();
      $this->output();
    }
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