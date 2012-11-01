<?php
class RunCommand {
  public function execute() {
    Lock::execute();
    for (;;) {
      $GLOBALS['VERSION'] = $this->getVersion();
      $processor = new JingdongCategoryListProcessor;
      $processor->execute();
      $this->checkCategory();
      $this->cleanHistory();
      $this->cleanProductPropertyValue();
      $this->cleanPropertyKey();
      $this->cleanPropertyValue();
      if ($GLOBALS['VERSION'] % 100 === 0) {
        $this->cleanMerchant();
      }
      $this->upgradeVersion();
    }
  }

  private function getVersion() {
    return file_get_contents(ROOT_PATH.'data/version');
  }

  private function checkCategory() {
    $categoryList = Db::getAll(
      'SELECT id FROM category WHERE version != ?', $GLOBALS['version']
    );
    foreach ($categoryList as $category) {
      $productId = Db::getColumn(
        'SELECT id FROM product WHERE category_id = ? LIMIT 1', $category['id']
      );
      if ($productId === false) {
        Db::delete('DELETE FROM category WHERE id = ?', $category['id']);
        continue;
      }
      $categoryBuilder = new JingdongCategoryBuilder;
      $categoryBuilder->execute($category['id']);
    }
  }

  private function upgradeVersion() {
    file_put_contents(ROOT_PATH.'data/version', ++$GLOBALS['VERSION']);
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
      $GLOBALS['VERSION']
    );
  }

  private function cleanPropertyKey() {
    Db::execute(
      'DELETE FROM property_key WHERE version != ?',
      $GLOBALS['VERSION']
    );
  }

  private function cleanPropertyValue() {
    Db::execute(
      'DELETE FROM property_value WHERE version != ?',
      $GLOBALS['VERSION']
    );
  }
}