<?php
class SyncShoppingProperty {
  public static function getPropertyList($categoryId) {
    $keyList = Db::getAll(
      'SELECT * FROM property_key WHERE category_id = ?', $categoryId
    );
    $result = array('key_list' => array(), 'value_list' => array());
    foreach ($keyList as $key) {
      DbConnection::connect('shopping');
      $shoppingKey = Db::getRow(
        'SELECT * FROM property_key WHERE name = ?', $key['name']
      );
      if ($shoppingKey === false) {
        Db::insert('property_key', array('name' => $key['name']));
        $shoppingKeyId = Db::getLastInsertId();
        ShoppingCommandFile::insertPropertyKey($shoppingKeyId, $key['name']);
      }
      DbConnection::close();
      $result['key_list'][$key['id']] = $key;
      $valueList = Db::getAll(
        'SELECT * FROM property_value WHERE key_id = ?', $key['id']
      );
      DbConnection::connect('shopping');
      foreach ($valueList as $value) {
        $shoppingValue = Db::getRow(
          'SELECT * FROM property_value WHERE key_id = ? AND name = ?',
          $shoppingKeyId, $value['name']
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
      DbConnection::close();
    }
  }
}