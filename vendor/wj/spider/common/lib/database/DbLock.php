<?php
class DbLock {
  public static function insert($processId) {
    Db::execute('INSERT INTO `lock`(process_id) VALUES(?)', $processId);
  }

  public static function getAll() {
    return Db::getAll('SELECT * FROM `lock`');
  }

  public static function deleteOthers($processId) {
    Db::execute('DELETE FROM `lock` WHERE process_id != ?', $processId);
  }

  public static function createTable() {
    $sql = "CREATE TABLE `lock` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `process_id` int(11) unsigned DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  }
}