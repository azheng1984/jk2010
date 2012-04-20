<?php
class Lock {
  public static function execute() {
    $lockList = DbLock::getAll();
    self::check();
  }

  private static function check() {
    if (function_exists('posix_getpid') === false) {
      return null;
    }
    posix_getpid();
  }

  private static function check($lockList) {
    if (processId === null && count($lockList) !== 0) {
      throw new Exception('lock fail');
    }
  }

  private static function checkByPs($processIdList) {
    foreach ($processIdList as $item) {
      $output = shell_exec("ps -p {$item['process_id']}");
      if (strstr($output, $item['process_id']) !== false) {
        throw new Exception('lock fail');
      }
    }
    DbLock::deleteOthers($processId);
  }
}