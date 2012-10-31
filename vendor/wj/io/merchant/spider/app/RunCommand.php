<?php
class RunCommand {
  public function execute() {
    Lock::execute();
    for (;;) {
      $GLOBALS['SPIDER_VERSION'] = $this->getVersion();
      $processor = new JingdongCategoryListProcessor;
      $processor->execute();
      $this->cleanHistory();
      $this->cleanProductPropertyValue();
      $this->cleanPropertyKey();
      $this->cleanPropertyValue();
      if ($GLOBALS['SPIDER_VERSION'] % 100 === 0) {
        $this->cleanCategory();
        $this->cleanMerchant();
      }
    }
  }

  private function getVersion() {
    return 0;
  }

  private function cleanHistory() {
    Db::execute("DELETE FROM history WHERE last_ok_date < '"
      .date('Y-m-d', time() - (30 * 24 * 60 * 60)).'"');
    Db::execute('DELETE FROM history WHERE _status = 404');
  }

  private function cleanCategory() {
    $categoryList = Db::getAll("SELECT id FROM category");
    foreach ($categoryList as $category) {
      $productId = Db::getColumn(
        'SELECT id FROM product WHERE category_id = ? LIMIT 1', $category['id']
      );
      if ($productId === false) {
        Db::delete('DELETE FROM category WHERE id = ?', $category['id']);
      }
    }
  }

  private function cleanMerchant() {
    $merchantList = Db::getAll("SELECT id FROM merchant");
    foreach ($merchantList as $merchant) {
      $productId = Db::getColumn(
        'SELECT id FROM product WHERE merchant_id = ? LIMIT 1', $merchant['id']
      );
      if ($productId === false) {
        Db::delete('DELETE FROM merchant WHERE id = ?', $merchant['id']);
      }
    }
  }

  private function cleanProductPropertyValue() {
    Db::execute(
      'DELETE FROM product_property_value WHERE version != ?',
      $GLOBALS['SPIDER_VERSION']
    );
  }

  private function cleanPropertyKey() {
    Db::execute(
      'DELETE FROM property_key WHERE version != ?',
      $GLOBALS['SPIDER_VERSION']
    );
  }

  private function cleanPropertyValue() {
    Db::execute(
      'DELETE FROM property_value WHERE version != ?',
      $GLOBALS['SPIDER_VERSION']
    );
  }
}