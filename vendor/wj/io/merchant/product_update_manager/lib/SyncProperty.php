<?php
class SyncProperty {
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
    $keyShoppingIdList = array();
    foreach ($keyList as $key) {
      $shoppingKey = Db::getRow(
        'SELECT * FROM property_key WHERE name = ?', $key['name']
      );
      $shoppingKeyId = null;
      if ($shoppingKey === false) {
        Db::insert('property_key', array(
          'name' => $key['name'], 'version' => $GLOBALS['VERSION']
        ));
        $shoppingKeyId = Db::getLastInsertId();
        CommandSyncFile::insertPropertyKey($shoppingKeyId, $key['name']);
      } else {
        $shoppingKeyId = $shoppingKey['id'];
        //TODO check update manager version
      }
      $keyShoppingIdList[$key['id']] = $shoppingKeyId;
      $result['key_list'][$key['id']] = $key;
    }
    //key/value 操作分离，可以缩小同步文件
    foreach ($keyList as $key) {
      $shoppingKeyId = $keyShoppingIdList[$key['id']];
      DbConnection::connect($merchantName);
      $valueList = Db::getAll(
        'SELECT * FROM property_value WHERE key_id = ?', $key['id']
      );
      DbConnection::close();
      foreach ($valueList as $value) {
        $shoppingValue = Db::getRow(
          'SELECT * FROM property_value WHERE key_id = ? AND name = ?',
          $shoppingKeyId, $value['name']
        );
        $shoppingValueId = null;
        if ($shoppingValue === false) {
          Db::insert('property_value', array(
            'key_id' => $shoppingKeyId,
            'name' => $value['name'],
            'version' => $GLOBALS['VERSION']
          ));
          $shoppingValueId = Db::getLastInsertId();
          CommandSyncFile::insertPropertyValue(
            $shoppingValueId, $shoppingKeyId, $value['name']
          );
        }
        if ($shoppingValue !== false) {
          $shoppingValueId = $shoppingValue['id'];
        }
        $value['shopping_id'] = $shoppingValueId;
        $result['value_list'][$value['id']] = $value;
      }
    }
    return $result;
  }
}