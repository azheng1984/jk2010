<?php
class JingdongCategoryBuilder {
  private $categoryId;

  public function execute($id) {
    $this->id = $id;
    //初始化 shopping portal category
    $this->executeHistory();
    $this->checkProduct();
    $this->upgradeCategoryVersion();
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

  private $keyList;
  private $valueList;

  private function initializePropertyList() {
    $keyList = Db::getAll(
      'SELECT * FROM property_key WHERE category_id = ? AND version = ?',
      $this->categoryId,
      $GLOBALS['VERSION']
    );
    $this->keyList = array();
    $this->valueList = array();
    foreach ($keyList as $key) {
      $this->keyList[$key['id']] = $key;
      //初始化 shopping portal key
      $valueList = Db::getAll(
        'SELECT * FROM property_value WHERE key_id = ? AND version = ?',
         $key['id'], 
         $GLOBALS['VERSION']
      );
      foreach ($valueList as $value) {
        //初始化 shopping portal value
        $this->valueList[$value['id']] = $value;
      }
    }
  }

  private function checkProduct() {
    $this->initializePropertyList();
    //持续同步（同步和增量数据所要运算是持续存在的（低优先级）（多线程计算由操作系统负责平衡））
    $productList = Db::getAll(
      'SELECT * FROM product WHERE category_id = ?', $this->categoryId
    );
    foreach ($productList as $product) {
      $valueList = Db::getAll(
        'SELECT * FROM product_property_value WHERE merchant_product_id = ?',
        $product['merchant_product_id']
      );
      $propertyList = array();
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
      }
      ksort($propertyList);
      $shoppingPropertyList = array();
      foreach ($propertyList as $property) {
        $item = $property['name']."\n";
        ksort($property['value_list']);
        $item .= implode("\n", $propertyList);
        $shoppingPropertyList []= $item;
      }
      $shoppingPropertyListByText = implode("\n", $shoppingPropertyList);
      if ($product['shopping_product_id'] === null) {
        //TODO:同步本地 shopping portal
        //TODO:同步本地 shopping search
        //TODO:通过 isset 和 unset + amount 来检测 keywords list 关键词
        //TODO:value_id_list 是已经排序的，数字排序后直接比较
        //TODO:输出“指令日志” 到文件
      }
      DbConnection::connect('shopping_portal');
      $shoppingProduct = Db::getRow(
        'SELECT * FROM product WHERE id = ?',
        $product['shopping_product_id']
      );
      if ($shoppingProduct['property_list'] === $shoppingPropertyListByText) {
        
      }
    }
    //TODO:压缩并移动指令文件到 ftp 服务器，删除未压缩的文件
    //TODO:向 shopping portal & search 服务器发送 “拉” 信号
  }
}