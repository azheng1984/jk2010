<?php
//TODO:用事务防止多线程 local shopping portal 操作冲突
class JingdongCategoryBuilder {
  private $categoryId;
  private $categoryName;
  private $keyList;
  private $valueList;
  private $shoppingCategoryId;
  private $output = array();
  private $productSearchOutput = array();
  private $imageList = array();

  public function execute($categoryId, $categoryName) {
    $this->categoryId = $categoryId;
    $this->categoryName = $categoryName;
    $this->shoppingCategoryId = SyncShoppingCategory::getCategoryId($categoryName);
    $this->setShoppingCategoryId();
    $this->executeHistory();
    $this->checkProduct();
    $this->upgradeCategoryVersion();
    $this->output();
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

  private function getMerchantPath($merchantId) {
    $name = Db::getColumn('SELECT name FROM merchant WHERE id = ?', $merchantId);
    return "京东商城\n".$name;
  }

  private function initializePropertyList() {
    $keyList = Db::getAll(
      'SELECT * FROM property_key WHERE category_id = ? AND version = ?',
      $this->categoryId,
      $GLOBALS['VERSION']
    );
    $this->keyList = array();
    $this->valueList = array();
    foreach ($keyList as $key) {
      DbConnection::connect('shopping');
      $shoppingKey = Db::getRow(
        'SELECT * FROM property_key WHERE name = ?', $key['name']
      );
      if ($shoppingKey === false) {
        Db::insert('property_key', array('name' => $key['name']));
        $shoppingKeyId = Db::getLastInsertId();
        $this->output []= "INSERT INTO property_key(id, name) VALUES("
          .$shoppingKeyId.", "
          .DbConnection::get()->quote($key['name']).")";
      }
      DbConnection::connect('jingdong');
      $this->keyList[$key['id']] = $key;
      $valueList = Db::getAll(
        'SELECT * FROM property_value WHERE key_id = ? AND version = ?',
         $key['id'], 
         $GLOBALS['VERSION']
      );
      foreach ($valueList as $value) {
        DbConnection::connect('shopping');
        $shoppingValue = Db::getRow(
          'SELECT * FROM property_value WHERE key_id = ? AND name = ?',
          $shoppingKeyId,
          $value['name']
        );
        if ($shoppingKey === false) {
          Db::insert('property_value', array(
            'key_id' => $shoppingKeyId,
            'name' => $value['name']
          ));
          $shoppingValueId = Db::getLastInsertId();
          $this->output []= "INSERT INTO property_value(id, key_id, name) VALUES("
            .$shoppingValueId.", "
            .$shoppingKeyId.", "
            .DbConnection::get()->quote($value['name']).")";
        }
        DbConnection::connect('jingdong');
        $value['shopping_id'] = $shoppingValueId;
        $this->valueList[$value['id']] = $value;
      }
    }
  }

  private function getImagePath($root) {
    $folder = $this->getImageFolder();
    $levelOne = floor($folder / 10000);
    $folder = $root.$levelOne;
    if (is_dir($folder)) {
      mkdir($folder);
    }
    $levelTwo = $folder % 10000;
    $folder = $folder.'/'.$levelTwo;
    if (is_dir($folder)) {
      mkdir($folder);
    }
    return $root;
  }

  private function getImageFolder() {
    DbConnection::connect('io_merchant_spider');
    $row = Db::getRow('SELECT * FROM image_folder ORDER BY amount LIMIT 1');
    if ($row === false || $row['amount'] >= 10000) {
      Db::insert('image_folder', array());
      return Db::getLastInsertId();
    }
    Db::update(
      'image_folder', array('amount' => ++$row['amount']), 'id = ?', $row['id']
    );
    return $row['id'];
  }

  private function getWordList($text) {
    //TODO
    $list = explode(' ', $text);
    return array_unique($list);
  }

  private function syncProuctSearch($keywords, $shoppingValueIdTextList, $price) {
    //TODO:update
    DbConnection::connect('product_search');
    $keywordList = $this->getWordList($keywords);
    Db::insert('product', array(
      'category_id' => $this->shoppingCategoryId,
      'price_from_x_100' => $price,
      'value_id_list' => $shoppingValueIdTextList,
      'keyword_list' => implode(' ', $keywordList)
    ));
    $sql = 'INSERT INTO product_search(category_id, price_from_x_100, value_id_list, keyword_list) VALUES()';
    $this->productSearchOutput []= $sql;
    DbConnection::connect('jingdong');
  }

  private function checkProduct() {
    $this->initializePropertyList();
    $productList = Db::getAll(
      'SELECT * FROM product WHERE category_id = ?', $this->categoryId
    );
    foreach ($productList as $product) {
      if ($product['version'] < $GLOBALS['VERSION']) {
        //delete product
      }
      $valueList = Db::getAll(
        'SELECT * FROM product_property_value WHERE merchant_product_id = ?',
        $product['merchant_product_id']
      );
      $propertyList = array();
      $shoppingValueIdList = array();
      foreach ($valueList as $value) {
        $value = $this->valueList[$value['property_value_id']];
        $key = $this->keyList[$value['key_id']];
        if (!isset($propertyList[$key['_index']])) {
          $propertyList[$key['_index']] = array(
            'name' => $key['name'], 'value_list' => array()
          );
        }
        $propertyList[$key['_index']]['value_list'][$value['_index']]
          = $value['name'];
        $shoppingValueIdList[] = $value['shopping_id'];
      }
      sort($shoppingValueIdList);
      ksort($propertyList);
      $shoppingPropertyList = array();
      foreach ($propertyList as $property) {
        $item = $property['name']."\n";
        ksort($property['value_list']);
        $item .= implode("\n", $property['value_list']);
        $shoppingPropertyList []= $item;
      }
      $shoppingPropertyTextList = implode("\n", $shoppingPropertyList);
      $merchantPath = $this->getMerchantPath($product['merchant_id']);
      $image = ImageDb::get($this->categoryId, $product['id']);
      $imagePath = $this->getImagePath();
      if ($product['shopping_product_id'] === null) {
        Db::insert('product', array(
          'merchant_path' => $merchantPath,
          'merchant_uri_argument_list' => $product['merchant_product_id'],
          'price_from_x_100' => $product['price_from_x_100'],
          'image_path' => $imagePath,
          'image_digest' => md5($image),
        ));
        $shoppingProductId = Db::getLastInsertId();
        $imageStagingFolder = '/home/azheng/image_staging/jingdong/';
        file_put_contents(
          $imageStagingFolder.$imagePath.$shoppingProductId.'.jpg', $image
        );
        $this->syncImage($image, $product['id'], $shoppingProductId);
        $this->output .= 'INSERT INTO product';//TODO
        $shoppingValueIdTextList = implode(' ', $shoppingValueIdList);
        $keywords = $product['title'];
        $keywords .= ' '.$this->categoryName;
        $keywords .= ' '.$shoppingPropertyTextList;
        $keywords .= ' '.$merchantPath;
        $this->syncProuctSearch(
          $keywords, $shoppingValueIdTextList, $product['price_from_x_100']
        );
        continue;
      }
      //TODO:update 本地 shopping portal
      DbConnection::connect('shopping_portal');
      $shoppingProduct = Db::getRow(
        'SELECT * FROM product WHERE id = ?',
        $product['shopping_product_id']
      );
      if ($shoppingProduct['property_list'] !== $shoppingPropertyTextList) {
        
      }
      if ($shoppingProduct['category_name'] !== $this->categoryName) {
      }
      if ($shoppingProduct['price_from_x_100'] !== $product['price_from_x_100']) {
      }
      if ($product['is_image_updated']) {
      }
      DbConnection::connect('shopping_product_search');
      $shoppingProductSearchProduct = Db::getRow(
        'SELECT * FROM product WHERE id = ?',
        $product['shopping_product_id']
      );
      if ($shoppingProductSearchProduct['value_id_list'] !== $shoppingValueIdTextList) {
        //TODO:update value id list
      }
      $keywordList = explode(' ', $shoppingProductSearchProduct['keyword_list']);
      $keywordListByKey = array();
      foreach ($keywordList as $keyword) {
        $keywordListByKey[$keyword] = true;
      }
      $currentKeywordList = array_unique(explode(' ', $keywords));
      $isUpdated = false;
      foreach ($currentKeywordList as $item) {
        if (isset($keywordListByKey[$item])) {
          unset($keywordListByKey[$itme]);
          continue;
        }
        $isUpdated = true;
        break;
      }
      if (count($keywordListByKey) !== 0) {
        $isUpdated = true;
      }
      if ($isUpdated) {
        //TODO:update keyword list
      }
      //TODO:update product search db & record sql
    }
  }

  private function output() {
    //TODO:压缩并移动指令文件到 ftp 服务器
    system('zip');
    //TODO:向 shopping portal & search 服务器发送 ready 信号(via mysql（和其它数据库分离）)，等待传输
    Db::update();
  }
}