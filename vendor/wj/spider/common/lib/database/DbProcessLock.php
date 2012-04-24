<?php
class DbProcessLock {
  public static function insert($pid) {
    Db::insert('process_lock', array('pid' => $pid));
  }

  public static function getAll() {
    return Db::getAll('SELECT * FROM process_lock');
  }

  public static function deleteOthers($pid) {
    Db::delete('process_lock', 'pid != ?', $pid);
  }

  public static function tryCreateTable() {
    if (Db::getColumn("SHOW TABLES LIKE 'process_lock'") === false) {
      $sql = 'CREATE TABLE `process_lock` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `pid` int(11) unsigned DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1';
      Db::execute($sql);
    }
  }
}