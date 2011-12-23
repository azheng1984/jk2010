<?php
class DbProperty {
  public static function getOrNewKeyId($tablePrefix, $keyName) {
    $sql = 'SELECT id FROM '.$tablePrefix.'_property_key WHERE `name` = ?';
    $id = Db::getColumn($sql, $keyName);
    if ($id === false) {
      $sql = 'REPLACE INTO '.$tablePrefix.'_property_key(`name`, is_updated)'
        .' VALUES(?, 1)';
      Db::execute($sql, $keyName);
      return DbConnection::get()->lastInsertId();
    }
    return $id;
  }

  public static function getOrNewValueId($tablePrefix, $keyId, $valueName) {
    $sql = 'SELECT id FROM '.$tablePrefix.'_property_value'
      .' WHERE key_id = ? AND `name` = ?';
    $id = Db::getColumn($sql, $keyId, $valueName);
    if ($id === false) {
      $sql = 'REPLACE INTO '.$tablePrefix.'_property_value'
        .'(key_id, `name`, is_updated) VALUES(?, ?, 1)';
      Db::execute($sql, $keyId, $valueName);
      return DbConnection::get()->lastInsertId();
    }
    return $id;
  }

  public static function expireAll($tablePrefix) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_property_key SET is_updated = 0'
    );
    Db::execute(
      'UPDATE '.$tablePrefix.'_property_value SET is_updated = 0'
    );
  }

  public static function deleteExpiredItems($tablePrefix) {
    Db::execute(
      'DELETE '.$tablePrefix.'_property_key WHERE is_updated = 0'
    );
    Db::execute(
      'DELETE '.$tablePrefix.'_property_value WHERE is_updated = 0'
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