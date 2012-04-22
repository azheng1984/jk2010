<?php
class DbCategory {
  public static function getOrNewId($name, $parentId = 0) {
    $id = Db::getColumn(
      'SELECT id FROM category WHERE parent_id = ? AND `name` = ?',
      $parentId, $name
    );
    if ($id === false) {
      Db::execute(
        'INSERT INTO category(parent_id, `name`) VALUES(?, ?)', $parentId, $name
      );
      return DbConnection::get()->lastInsertId();
    }
    return $id;
  }

  public static function tryCreateTable() {
    if (Db::getColumn("SHOW TABLES LIKE 'category'") === false) {
      $sql = 'CREATE TABLE `category` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `parent_id` int(11) unsigned DEFAULT NULL,
        `name` varchar(255) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `parent_id&name` (`parent_id`,`name`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
      Db::execute($sql);
    }
  }
}