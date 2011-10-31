<?php
class DbProperty {
  public static function getOrNewKeyId($tablePrefix, $key) {
    $sql = 'SELECT id FROM '.$tablePrefix.'_property_key WHERE `key` = ?';
    $id = Db::getColumn($sql, $key);
    if ($id === false) {
      $sql = 'REPLACE INTO '.$tablePrefix.'_property_key(`key`, is_update)'
        .' VALUES(?, 1)';
      Db::execute($sql, $key);
      return DbConnection::get()->lastInsertId();
    }
    return $id;
  }

  public static function getOrNewValueId($tablePrefix, $keyId, $value) {
    $sql = 'SELECT id FROM '.$tablePrefix.'_property_value'
      .' WHERE key_id = ? AND `value` = ?';
    $id = Db::getColumn($sql, $keyId, $value);
    if ($id === false) {
      $sql = 'REPLACE INTO '.$tablePrefix.'_property_value'
        .'(key_id, `value`, is_update) VALUES(?, ?, 1)';
      Db::execute($sql, $keyId, $value);
      return DbConnection::get()->lastInsertId();
    }
    return $id;
  }

  public static function expireAll($tablePrefix) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_property_key SET is_update = 0'
    );
    Db::execute(
      'UPDATE '.$tablePrefix.'_property_value SET is_update = 0'
    );
  }

  public static function deleteOldItems($tablePrefix) {
    Db::execute(
      'DELETE '.$tablePrefix.'_property_key WHERE is_update = 0'
    );
    Db::execute(
      'DELETE '.$tablePrefix.'_property_value WHERE is_update = 0'
    );
  }

  public static function createTable($tablePrefix) {
    $table = Db::getColumn('SHOW TABLES LIKE ?', $tablePrefix.'_property_key');
    if ($table === false) {
      $sql = "CREATE TABLE `".$tablePrefix."_property_key` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `key` varchar(63) NOT NULL,
        `is_update` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `key` (`key`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
    $table = Db::getColumn(
      'SHOW TABLES LIKE ?', $tablePrefix.'_property_value'
    );
    if ($table === false) {
      $sql = "CREATE TABLE `".$tablePrefix."_property_value` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `key_id` int(11) unsigned NOT NULL,
        `value` varchar(255) NOT NULL,
        `is_update` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `key_id-value` (`key_id`,`value`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}