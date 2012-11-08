<?php
class SyncShoppingProperty {
  public static function getPropertyList($categoryId) {
    
  }

  private function initialize() {
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
}