<?php
class BuildCommand {
  private $properties = array();
  private $propertyKeyIndex = array();
  private $propertyKeyMapping = array('系统' => '操作系统', '网络' => '制式');
  private $propertyValueMapping = array('电容屏触屏' => '电容触屏', '电阻屏触屏' => '电阻触屏', 'WindowsMobile' => 'Windows Mobile',
   '联通3G' => 'WCDMA', '电信3G' => 'CDMA2000', '移动3G'=> 'TD-SCDMA');
  private $manualBrands = array(
    '1000530369' => 'U73',
    '1000468567' => 'M228',
  );
  private $iphoneCollection = array(
    '颜色' => array('黑色' => '9114', '白色' => '9163'),
    '内存' => array('16GB', '32GB'),
    '套餐' => array('联通'),
  );
  private $iphone4Id;
  private $iphoneMapping = array(
    '317360' => array('黑色', '16G', true),
    '317363' => array('黑色', '32G', true),
    '391254' => array('白色', '16G', true),
    '293275' => array('白色', '32G', true),
    '292790' => array('黑色', '32G', false),
    '292497' => array('黑色', '16G', false),
    '293276' => array('白色', '16G', false),
    '391732' => array('白色', '32G', false)
  );

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
    $this->initialize();
    Db::execute('USE jingdong_staging');
    $sql = 'SELECT * FROM product_recognition_info';
    $items = Db::getAll($sql);
    foreach ($items as $item) {
      $this->push($item['product_id'], $item['brand'], $item['model']);
    }
  }

  public function execute2() {
    //foreach 获取爬虫更新的产品
    //parse 后对比更新，如果和原记录一样，又没有要求强制更新，continue; 如果是新记录,insert
    //识别产品，属性映射（包括图片和分类），如果不需要更新，continue
    //end foreach
    //生成更新包(增量/全量，更新脚本)
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