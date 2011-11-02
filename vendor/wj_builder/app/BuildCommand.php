<?php
class BuildCommand {
  private function initialize() {
    `cd ~/wj_img/2;rm *`;
    Db::execute('USE wj');
    Db::execute('delete from mobile_phone_product');
    Db::execute('delete from mobile_phone_merchant');
    $items = Db::getAll('SELECT * FROM mobile_phone_property_key');
    foreach ($items as $item) {
      $this->properties[$item['key']] = array('_id' => $item['id']);
      $this->propertyKeyIndex[$item['id']] = $item['key'];
    }
    $items = Db::getAll('SELECT * FROM mobile_phone_property_value');
    foreach ($items as $item) {
      $key = $this->propertyKeyIndex[$item['key_id']];
      $this->properties[$key][$item['value']] = $item;
    }
  }

  public function execute() {
    $tablePrefix = 'food';
    while (($item = DbProductUpdate::get($tablePrefix)) !== false) {
      $class = 'Product'.ucfirst($item['type']);
      echo $class;
      exit;
      $updater = new $class;
      $updater->execute($item);
      DbProductUpdate::delete($tablePrefix, $item['id']);
    }
  }

  private function push($id, $brand, $model) {
    if ($model === null) {
      if (isset($this->manualBrands[$id])) {
        $model = $this->manualBrands[$id];
      }
    }
    $brand = str_replace('（', '(', $brand);
    $brand = str_replace('）', ')', $brand);
    if ($brand === '苹果(Apple)' && $model = 'iPhone 4') {
      //$collectionValue = $this->iphoneCollection[$id];
    }
    $keywords = array('手机', $brand, $model);
    Db::execute('USE jingdong');
    $sql = 'SELECT v.key_id, v.value FROM product_property_value p LEFT JOIN property_value v ON p.property_value_id = v.id WHERE product_id = ?';
    $items = Db::getAll($sql, $id);
    $propertyValueList = array();
    foreach ($items as $item) {
      if ($item['value'] === '不限') {
        continue;
      }
      $keywords[] = $item['value'];
      if (isset($this->propertyValueMapping[$item['value']])) {
        $item['value'] = $this->propertyValueMapping[$item['value']];
      }
      $sql = 'SELECT `key` FROM property_key WHERE id = ?';
      $key = Db::getColumn($sql, $item['key_id']);
      if (isset($this->propertyKeyMapping[$key])) {
        $key = $this->propertyKeyMapping[$key];
      }
      if (!isset($this->properties[$key])) {
        continue;
      }
      if (!isset($this->properties[$key][$item['value']])) {
        $this->insertPropertyValue($key, $this->properties[$key]['_id'], $item['value']);
        Db::execute('USE jingdong');
      }
      $propertyValueList[] = $this->properties[$key][$item['value']]['id'];
    }
    $sql = 'SELECT promotion_price FROM price WHERE product_id = ?';
    $price = Db::getColumn($sql, $id);
    Db::execute('USE wj');
    if ($brand === '苹果(Apple)' && $model = 'iPhone 4') {
      $sql = 'INSERT INTO `mobile_phone_product`(`brand`, model, property_list, lowest_price, keywords) VALUES(?, ?, ?, ?, ?)';
      Db::execute($sql, $brand, $model, implode(',', $propertyValueList), $price, implode(',', $keywords));
      $collectionValue = var_export($this->iphoneCollection, true);
    } else {
      $sql = 'INSERT INTO `mobile_phone_product`(`brand`, model, property_list, lowest_price, keywords) VALUES(?, ?, ?, ?, ?)';
      Db::execute($sql, $brand,$model, implode(',', $propertyValueList), $price, implode(',', $keywords));
    }
    $connection = DbConnection::get();
    $wjId = $connection->lastInsertId();
    if ($brand === '苹果(Apple)' && $model = 'iPhone 4') {
      $this->iphone4Id = $wjId;
    }
    $sql = 'INSERT INTO global_product_index(category_id, product_id) VALUES(2, ?)';
    Db::execute($sql, $wjId);
    $this->addMerchant($wjId, $id, $price);
    $this->copyImage($id, $wjId);
  }

  private function insertPropertyValue($key, $keyId, $value) {
    Db::execute('USE wj');
    $sql = 'INSERT INTO mobile_phone_property_value(key_id, value) VALUES(?, ?)';
    Db::execute($sql, $keyId, $value);
    $connection = DbConnection::get();
    $id = $connection->lastInsertId();
    $this->properties[$key][$value] = array('id' => $id);
  }

  private function copyImage($sourceProductId, $productId) {
     copy('/home/wz/spider/image/jingdong/32/'.$sourceProductId.'.jpg',
     '/home/wz/wj_img/2/'.$productId.'.jpg');
  }

  private function addMerchant($productId, $merchantProductId, $price) {
    $url = 'http://www.360buy.com/product/'.$merchantProductId.'.html';
    $sql = 'INSERT INTO mobile_phone_merchant(product_id, merchant_id, url, price)'
      .' VALUES(?, ?, ?, ?)';
    Db::execute($sql, $productId, 1, $url, $price);
  }
}