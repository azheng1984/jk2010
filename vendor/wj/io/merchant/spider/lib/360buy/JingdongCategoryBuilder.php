<?php
//TODO:用事务防止多线程 insert category/property 冲突
class JingdongCategoryBuilder {
  private $categoryId;
  private $categoryName;
  private $keyList;
  private $valueList;
  private $shoppingCategoryId;
  private $output = array();
  private $imageList = array();

  public function execute($categoryId, $categoryName) {
    $this->categoryId = $categoryId;
    $this->categoryName = $categoryName;
    $this->setShoppingCategoryId();
    $this->executeHistory();
    $this->checkProduct();
    $this->upgradeCategoryVersion();
    $this->output();
  }

  private function output() {
    //TODO:压缩并移动指令文件到 ftp 服务器
    //TODO:向 shopping portal & search 服务器发送 ready 信号
  }

  private function setShoppingCategoryId() {
    DbConnection::connect('shopping');
    $shoppingCategory = Db::getRow(
      'SELECT * FROM category WHERE name = ?', $this->categoryName
    );
    if ($shoppingCategory === false) {
      Db::insert(
        'category', array('name' => $this->categoryName), $this->shoppingCategoryId
      );
      $this->shoppingCategoryId = Db::getLastInsertId();
      $this->output []= "INSERT INTO category(id, name) VALUES("
        .$this->shoppingCategoryId.", "
        .DbConnection::get()->quote($this->categoryName).")";
      return;
    }
    $this->shoppingCategoryId = $shoppingCategory['id'];
    DbConnection::connect('jingdong');
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

  private function checkProduct() {
    $this->initializePropertyList();
    $productList = Db::getAll(
      'SELECT * FROM product WHERE category_id = ?', $this->categoryId
    );
    foreach ($productList as $product) {
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
        $item .= implode("\n", $propertyList);
        $shoppingPropertyList []= $item;
      }
      $shoppingPropertyTextList = implode("\n", $shoppingPropertyList);
      //TODO:sync merchant
      $shoppingMerchantId = null;
      if ($product['shopping_product_id'] === null) {
        //TODO:同步图片（图片采用低 id 文件夹优先）
        //TODO:同步本地 shopping portal
        Db::insert('product', array(
          'merchant_id' => $shoppingMerchantId,
          'merchant_uri_argument_list' => $product['merchant_product_id'],
          'price_from_x_100' => $product['price_from_x_100'],
          'image_path' => 0,
          'image_digest' => 0,
        ));
        $this->output .= 'INSERT INTO product';
        $shoppingValueIdTextList = implode(' ', $shoppingValueIdList);
        //$categoryId;
        $keywords = $product['title'];
        $keywords .= ' '.$this->categoryName;
        $keywords .= ' '.$shoppingPropertyTextList;
        //TODO:关键字分词
        //TODO:同步本地 shopping search
        DbConnection::connect('product_search');
        Db::insert('product', array(
          'category_id' => $this->shoppingCategoryId,
          'price_from_x_100' => $product['price_from_x_100'],
          'value_id_list' => $shoppingValueIdTextList,
          'keyword_list' => $keywords
        ));
        DbConnection::connect('jingdong');
        //TODO:输出 “指令日志” 到文件
      }
      DbConnection::connect('shopping_portal');
      $shoppingProduct = Db::getRow(
        'SELECT * FROM product WHERE id = ?',
        $product['shopping_product_id']
      );
      if ($shoppingProduct['property_list']) {
        
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
      $isUpdate = false;
      foreach ($currentKeywordList as $item) {
        if (isset($keywordListByKey[$item])) {
          unset($keywordListByKey[$itme]);
          continue;
        }
        $isUpdate = true;
        break;
      }
      if (count($keywordListByKey) !== 0) {
        $isUpdate = true;
      }
    }
  }
}