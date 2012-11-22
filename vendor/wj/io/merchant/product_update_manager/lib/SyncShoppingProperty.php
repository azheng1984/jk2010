<?php
class SyncShoppingProperty {
  public static function getPropertyList(
    $categoryName, $merchantName, $version
  ) {
    DbConnection::connect($merchantName);
    $categoryId = Db::getColumn(
      'SELECT id FROM category WHERE name = ?', $categoryName
    );
    $keyList = Db::getAll(
      'SELECT * FROM property_key WHERE category_id = ? AND version = ?',
      $categoryId, $version
    );
    DbConnection::close();
    $result = array('key_list' => array(), 'value_list' => array());
    foreach ($keyList as $key) {
      $shoppingKey = Db::getRow(
        'SELECT * FROM property_key WHERE name = ?', $key['name']
      );
      if ($shoppingKey === false) {
        Db::insert('property_key', array('name' => $key['name']));
        $shoppingKeyId = Db::getLastInsertId();
        ShoppingCommandFile::insertPropertyKey($shoppingKeyId, $key['name']);
      }
      $result['key_list'][$key['id']] = $key;
      DbConnection::connect($merchantName);
      $valueList = Db::getAll(
        'SELECT * FROM property_value WHERE key_id = ?', $key['id']
      );
      DbConnection::close();
      foreach ($valueList as $value) {
        $shoppingValue = Db::getRow(
          'SELECT * FROM property_value WHERE'
            .' key_id = ? AND name = ? AND version = ?',
          $shoppingKeyId, $value['name'], $version
        );
        if ($shoppingValue === false) {
          Db::insert('property_value', array(
            'key_id' => $shoppingKeyId, 'name' => $value['name']
          ));
          $shoppingValueId = Db::getLastInsertId();
          ShoppingCommandFile::insertPropertyValue(
            $shoppingValueId, $shoppingKeyId, $value['name']
          );
        }
        $value['shopping_id'] = $shoppingValueId;
        $result['value_list'][$value['id']] = $value;
      }
    }
    return $result;
  }
}