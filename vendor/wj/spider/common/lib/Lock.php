<?php
class Lock {
  public static function execute() {
    $lockList = DbLock::getAll();
    $processId = null;
    if (function_exists('posix_getpid') === true) {
      $processId = posix_getpid();
    }
    if (count($lockList) !== 0 && $processId === null) {
      throw new Exception('lock fail');
    }
    DbLock::insert($processId);
    if ($processId === null) {
      return;
    }
    foreach ($lockList as $item) {
      $output = shell_exec("ps -p {$item['process_id']}");
      if (strstr($output, $item['process_id']) !== false) {
        throw new Exception('lock fail');
      }
    }
    DbLock::deleteOthers($processId);
  }
}