<?php
class Lock {
  public static function execute() {
    $processId = posix_getpid();
    DbLock::insert($processId);
    foreach (DbLock::getOthers($processId) as $otherProcessId) {
      $result = shell_exec("ps -p {$otherProcessId['process_id']}");
      if (strstr($result, $otherProcessId['process_id']) !== false) {
        return false;
      }
    }
    DbLock::deleteOthers($processId);
  }
}