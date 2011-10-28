<?php
class Lock {
  public static function execute() {
    if (!function_exists('posix_getpid')) {
      return;
    }
    $processId = posix_getpid();
    DbLock::insert($processId);
    foreach (DbLock::getOthers($processId) as $other) {
      $output = shell_exec("ps -p {$other['process_id']}");
      if (strstr($output, $other['process_id']) !== false) {
        throw new Exception('lock fail');
      }
    }
    DbLock::deleteOthers($processId);
  }
}