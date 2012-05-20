<?php
class ProductNewProcessor {
  private $wjProductId;
  private $categoryId;
  private $category;

  public function execute($productId, $category, $keyMapper, $valueMapper) {
    $this->category = $category;
    $propertyValueIdList = Db::getAll(
      'SELECT property_value_id, is_updated FROM '
        .'`electronic-product-property_value` WHERE product_id = ?',
      $productId
    );
    $propertyList = array();
    foreach ($propertyValueIdList as $item) {
      
      if ($item['is_updated'] === '0') {
        Db::execute('UPDATE property_key SET product_amount = product_amount - 1 WHERE id = ?', $item['']);
        //TODO decrease builder key product amount
        continue;
      }
      if ($item['is_new'] === '1') {
        //TODO: increase builder key product amount
      }
      $value = $valueMapper[$item['value_id']];
      $key = $keyMapper[$value['key_id']];
      $propertyValueId = $item['id'];
      if (isset($propertyList[$key['name']]) === false) {
        $propertyList[$key['name']] = array();
      }
      $propertyList[$key['name']][] = $value['name'];
    }
    $webPropertyList = array();
    foreach ($propertyList as $keyName => $valueNameList) {
      $webPropertyList[] = $keyName.implode("\n\t", $valueNameList);
    }
    $webPropertyListValue = implode("\n", $webPropertyList);
    $product = Db::getRow(
      'SELECT category_id, merchant_product_id, title,'
        .' image_md5, price_from_x_100, price_to_x_100, list_price_x_100,'
        .' sale_rank, index_time FROM `product` WHERE id = ?',
      $productId
    );//spider
    $this->syncWeb($product, $webPropertyListValue, $propertyList);
    $this->syncSearch($product, $webPropertyList);
  }

  private function syncWeb($product, $webPropertyListValue, $propertyList) {
    DbConnection::connect('builder');
    Db::insert('`wj_product-merchant_product`', array(
      'merchant_id' => 1,
      'merchant_product_id' => $product['merchant_product_id']
    ));
    $this->wjProductId = Db::getLastInsertId();
    DbConnection::connect('jingdong');
    $product['merchant_uri_argument_list'] = $product['merchant_product_id'];
    unset($product['merchant_product_id']);
    unset($product['sale_rank']);
    unset($product['index_time']);
    if ($webPropertyListValue !== '') {
      $product['property_list'] = $webPropertyListValue;
    }
    $product['category_name'] = $this->category['name'];
    $product['image_path'] = '/';
    Db::insert('product', $product);
    DbConnection::connect('jingdong');
  }

  private function syncSearch($product, $category, $keyMapper, $valueMapper) {
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
    $row['keyword_list'] = SegmentationService::execute();
    $row['key_id_list'] = array();
    //TODO set key_id_list
    $propertyList = array();
    foreach ($propertyList as $property) {
      $key = $property['key'];
      $value = $property['value'];
      $row['value_id_list_'] = array();
      //TODO set value_id_list
    }
    //TODO insert into search db
  }
}