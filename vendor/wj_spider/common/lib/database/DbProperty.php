<?php
class DbProperty {
  public static function getOrNewKeyId($tablePrefix, $key) {
    $sql = 'SELECT id FROM '.$tablePrefix.'_property_key WHERE `key` = ?';
    $id = Db::getColumn($sql, $key);
    if ($id === false) {
      $sql = 'INSERT INTO '.$tablePrefix.'(`key`) VALUES(?)';
      Db::execute($sql, $key);
      return DbConnection::get()->getLastInsertId();
    }
    return $id;
  }

  public static function getOrNewValueId($tablePrefix, $keyId, $value) {
    $sql = 'SELECT id FROM '.$tablePrefix.'_property_value'
      .' WHERE `value` = ? AND key_id = ?';
    $id = Db::getRow($sql, $value, $keyId);
    if ($id === false) {
      $sql = 'INSERT INTO '.$tablePrefix.'_property_value(key_id, `value`)'
        .' VALUES(?, ?)';
      Db::execute($sql, $keyId, $value);
      return DbConnection::get()->getLastInsertId();
    }
    return $id;
  }

  public static function createTable($tablePrefix) {
      if (
      Db::getColumn('show tables like ?', $tablePrefix.'_property_key') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_property_key` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `key` varchar(45) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `category_id|key` (`key`)
      ) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
    if (
      Db::getColumn('show tables like ?', $tablePrefix.'_property_value') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_property_value` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `key_id` int(11) unsigned DEFAULT NULL,
        `value` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `key_id|value` (`key_id`,`value`)
      ) ENGINE=InnoDB AUTO_INCREMENT=931 DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}