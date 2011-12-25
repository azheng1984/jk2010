<?php
class DbProperty {
  public static function getOrNewKeyId($tablePrefix, $keyName) {
    $sql = 'SELECT id, is_updated FROM '.$tablePrefix.'_property_key WHERE `name` = ?';
    $row = Db::getRow($sql, $keyName);
    if ($row !== false && $row['is_updated'] === '1') {
      return $row['id'];
    }
    if ($row === false) {
      $sql = 'INSERT INTO '.$tablePrefix.'_property_key(`name`, is_updated)'
        .' VALUES(?, 1)';
      Db::execute($sql, $keyName);
      return DbConnection::get()->lastInsertId();
    }
    $sql = 'UDPATE '.$tablePrefix
      .'_property_key SET is_updated = 1 WHERE id = ?';
    Db::execute($sql, $row['id']);
    return $row['id'];
  }

  public static function getOrNewValueId($tablePrefix, $keyId, $valueName) {
    $sql = 'SELECT id, is_updated FROM '.$tablePrefix.'_property_value'
      .' WHERE key_id = ? AND `name` = ?';
    $row = Db::get($sql, $keyId, $valueName);
    if ($row !== false && $row['is_updated'] === '1') {
      return $row['id'];
    }
    if ($row ===  false) {
      $sql = 'INSERT INTO '.$tablePrefix.'_property_value'
        .'(key_id, `name`, is_updated) VALUES(?, ?, 1)';
      Db::execute($sql, $keyId, $valueName);
      return DbConnection::get()->lastInsertId();
    }
    $sql = 'UDPATE '.$tablePrefix
      .'_property_value SET is_updated = 1 WHERE id = ?';
    Db::execute($sql, $row['id']);
    return $row['id'];
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
        `name` varchar(63) NOT NULL,
        `is_updated` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `key` (`name`)
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
        `name` varchar(255) NOT NULL,
        `is_updated` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `key_id-name` (`key_id`,`name`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}