<?php
class Lock {
  public static function execute() {
    $lockList = Db::getAll('SELECT * FROM process_lock');
    $processId = null;
    if (function_exists('posix_getpid') === true) {
      $processId = posix_getpid();
    }
    if (count($lockList) !== 0 && $processId === null) {
      self::fail();
    }
    Db::insert('process_lock', array('pid' => $processId));
    if ($processId === null) {
      return;
    }
    foreach ($lockList as $item) {
      $output = shell_exec("ps -p {$item['process_id']}");
      if (strstr($output, $item['process_id']) !== false) {
        self::fail();
      }
    }
    Db::delete('process_lock', 'pid != ?', $processId);
  }

  private static function fail() {
    throw new Exception('lock fail');
  }
}