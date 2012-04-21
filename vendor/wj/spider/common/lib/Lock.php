<?php
class Lock {
  public static function execute() {
    $lockList = DbLock::getAll();
    $processId = null;
    if (function_exists('posix_getpid') === true) {
      $processId = posix_getpid();
    }
    if (count($lockList) !== 0 && $processId === null) {
      self::fail();
    }
    DbLock::insert($processId);
    if ($processId === null) {
      return;
    }
    foreach ($lockList as $item) {
      $output = shell_exec("ps -p {$item['process_id']}");
      if (strstr($output, $item['process_id']) !== false) {
        self::fail();
      }
    }
    DbLock::deleteOthers($processId);
  }

  private static function fail() {
    throw new Exception('lock fail');
  }
}