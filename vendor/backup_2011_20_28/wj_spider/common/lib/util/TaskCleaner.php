<?php
class TaskCleaner {
  public static function clean() {
    $runningTask = DbTask::getRunning();
    if ($runningTask !== false) {
      DbTask::deleteByLargerThanId($runningTask['id']);
    }
    DbTask::setRunning($runningTask['id'], 0);
  }
}