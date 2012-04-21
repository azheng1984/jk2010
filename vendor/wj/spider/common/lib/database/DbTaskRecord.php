<?php
class DbTaskRecord {
  public static function getByTaskId($taskId) {
    $sql = 'SELECT * FROM task_record WHERE task_id = ?';
    return Db::getAll($sql, $taskId);
  }

  public static function deleteByTaskId($taskId) {
    $sql = 'DELETE FROM task_record WHERE task_id = ?';
    Db::execute($sql, $taskId);
  }

  public static function insert($taskId, $result) {
    $sql = 'INSERT INTO task_record(task_id, result, `time`)'
      .' VALUES(?, ?, NOW())';
    Db::execute($sql, $taskId, var_export($result, true));
  }

  public static function createTable() {
    $sql = "CREATE TABLE `task_record` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `task_id` bigint(20) unsigned NOT NULL,
      `time` datetime DEFAULT NULL,
      `result` blob,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  }
}