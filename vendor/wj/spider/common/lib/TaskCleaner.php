<?php
class TaskCleaner {
  public static function clean() {
    $runningTask = Db::getRow('SELECT id FROM task WHERE is_running = 1');
    if ($runningTask !== false) {
      Db::delete('task', 'id > ?', $runningTask['id']);
    }
    DbTask::setRunning($runningTask['id'], 0);
  }
}