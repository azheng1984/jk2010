<?php
class ProductNewProcessor {
  private $wjProductId;
  private $categoryId;

  public function execute($productId) {
    $propertyValueIdList = Db::getAll(
      'SELECT property_value_id FROM `electronic-product-property_value`'
        .' WHERE product_id = ?',
      $productId
    );
    $propertyList = array();
    foreach ($propertyValueIdList as $item) {
      $propertyValueId = $item['id'];
      $row = Db::getRow(
        'SELECT value.name as value_name, `key`.name as key_name'
          .' FROM `electronic-property_value` value'
          .' LEFT JOIN `electronic-property_key` as `key`'
          .' ON value.key_id = `key`.id WHERE value.id = ?', $propertyValueId
      );
      if (isset($propertyList[$row['key_name']]) === false) {
        $propertyList[$row['key_name']] = array();
      }
      $propertyList[$row['key_name']][] = $row['value_name'];
    }
    $webPropertyList = array();
    foreach ($propertyList as $keyName => $valueNameList) {
      $webPropertyList[] = $keyName.implode("\n\t", $valueNameList);
    }
    $webPropertyListValue = implode("\n", $webPropertyList);
    $product = Db::getRow(
      'SELECT category_id, merchant_product_id, title,'
        .' image_md5 price_from_x_100, price_to_x_100, list_price_x_100,'
        .' sale_rank, index_time FROM `electronic-product` WHERE id = ?',
      $productId
    );
    $this->syncWeb($product, $webPropertyListValue, $propertyList);
    $this->syncSearch($product);
  }

  private function syncWeb($product, $webPropertyListValue, $propertyList) {
    DbConnection::connect('builder');
    Db::insert('`wj_product-merchant_product`', array(
      'merchant_id' => 1,
      'merchant_product_id' => $product['merchant_product_id']
    ));
    $this->wjProductId = Db::getLastInsertId();
    DbConnection::connect('jingdong');
    $categoryName = Db::getColumn(
      'SELECT name FROM category WHERE id = ?', $product['catgory_id']
    );
    DbConnection::connect('web');
    $isNew = null;
    $categoryId = Db::bind(
      'category', array('name' => $categoryName), null, $isNew
    );
    if ($isNew) {
      Db::insert('category_mva', array('category_id' => $categoryId));
    }
    $product['merchant_uri_argument_list'] = $product['merchant_product_id'];
    unset($product['merchant_product_id']);
    unset($product['sale_rank']);
    unset($product['index_time']);
    if ($webPropertyListValue !== '') {
      $product['property_list'] = $webPropertyListValue;
    }
    $product['category_name'] = $categoryName;
    $product['image_path'] = '/';
    Db::insert('product', $product);
    DbConnection::connect('jingdong');
  }

  private function syncSearch($product) {
    $row = array();
    $row['id'] = $this->wjProductId;
    $row['category_id'] = $this->categoryId;
    $row['price_from_x_100'] = $product['price_from_x_100'];
    $row['sale_rank'] = $product['sale_rank'];
    $row['index_timestamp'] = strtotime($product['index_time']);
    if ($row['list_price_x_10'] !== null && $row['price_to_x_10'] === null) {
      $row['discount_x_10'] = ceil(
        $row['price_x_10'] / $row['list_price_x_10'] * 100
      );
    }
    SegmentationService::execute();
    array_unique();
  }
}