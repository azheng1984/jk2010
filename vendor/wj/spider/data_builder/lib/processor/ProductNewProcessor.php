<?php
class ProductNewProcessor {
  private $wjProductId;
  private $category;

  public function execute($productId, $category, $keyMapper, $valueMapper) {
    $this->category = $category;
    DbConnection::connect('spider');
    $propertyValueIdList = Db::getAll(
      'SELECT property_value_id, is_updated FROM '
        .'`electronic-product-property_value` WHERE product_id = ?',
      $productId
    );
    $propertyKeyList = array();
    $propertyValueList = array();
    foreach ($propertyValueIdList as $item) {
      $value = $valueMapper[$item['value_id']];
      $key = $keyMapper[$value['key_id']];
      if (isset($propertyList[$key['name']]) === false) {
        $propertyValueList[$key['name']] = array();
        $propertyKeyList[$key['name']] = $key;
      }
      $propertyValueList[$key['name']][] = $value;
    }
    DbConnection::connect('spider');
    $product = Db::getRow(
      'SELECT merchant_product_id, title,'
        .' image_md5, price_from_x_100, price_to_x_100, list_price_x_100,'
        .' sales_rank, index_time FROM `product` WHERE id = ?',
      $productId
    );
    $this->syncWeb($product, $propertyKeyList, $propertyValueList);
    $this->syncSearch($product, $propertyKeyList, $propertyValueList);
  }

  private function syncWeb($product, $keyList, $valueList) {
    $mvaPropertyList = array();
    $textPropertyList = array();
    foreach ($valueList as $keyName => $propertyValueList) {
      $webPropertyValueList = array();
      foreach ($propertyValueList as $value) {
        $webPropertyValueList[$value['index']] = $value;
      }
      if (count($webPropertyValueList) > 1) {
        ksort($webPropertyValueList);
      }
      if ($keyList[$keyName]['mva_index'] !== null) {
        $mvaPropertyList[$keyList[$keyName]['index']] =
          $keyName.implode("\n\t", $webPropertyValueList);
        continue;
      }
      $textPropertyList[$keyList[$keyName]['index']] =
        $keyName.implode("\n\t", $webPropertyValueList);
    }
    ksort($mvaPropertyList);
    ksort($textPropertyList);
    $product['property_list'] = implode("\n", $mvaPropertyList);
    if (count($textPropertyList) !== 0) {
      $product['property_list'] .= "\n".implode("\n", $textPropertyList);
    }
    DbConnection::connect('builder');
    Db::insert('`wj_product-merchant_product`', array(
      'merchant_id' => 1,
      'merchant_product_id' => $product['merchant_product_id']
    ));
    $this->wjProductId = Db::getLastInsertId();
    $product['id'] = $this->wjProductId;
    $product['category_id'] = $this->category['wj_id'];
    $product['merchant_uri_argument_list'] = $product['merchant_product_id'];
    unset($product['merchant_product_id']);
    unset($product['sales_rank']);
    unset($product['index_time']);
    $product['category_name'] = $this->category['name'];
    $product['image_path'] = '/';
    DbConnection::connect('web');
    Db::insert('product', $product);
  }

  private function syncSearch($product, $keyList, $valueList) {
    $row = array();
    $row['id'] = $this->wjProductId;
    $row['category_id'] = $this->category['wj_id'];
    $row['price_from_x_100'] = $product['price_from_x_100'];
    $row['sales_rank'] = $product['sales_rank'];
    $row['index_timestamp'] = strtotime($product['index_time']);
    if ($row['list_price_x_10'] !== null && $row['price_to_x_10'] !== null) {
      $row['discount_x_10'] = ceil(
        $row['price_x_10'] / $row['list_price_x_10'] * 100
      );
    }
    $row['keyword_list'] = implode(' ', array_unique(
      explode(' ', SegmentationService::execute())
    ));
    $keyIdList = array();
    $propertyList = array();
    foreach ($keyList as $keyName => $key) {
      if ($key['mva_index'] === null) {
        continue;
      }
      $keyIdList[] = $key['wj_id'];
      $valueIdList = array();
      foreach ($valueList[$key['name']] as $value) {
        $valueIdList = $value['wj_id'];
      }
      if (count($valueIdList) > 1 && $key['is_multiple'] === '0') {
        DbConnection::connect('web');
        Db::update(
          'property_key', array('is_multiple' => '1'), 'id = ?', $key['wj_id']
        );
        //TODO update memory
      }
      $row['value_id_list_'.$key['mva_index']] = implode(',', $valueIdList);
    }
    $row['key_id_list'] = implode(',', $keyIdList);
    DbConnection::connect('search');
    Db::insert('product', $row);
  }
}