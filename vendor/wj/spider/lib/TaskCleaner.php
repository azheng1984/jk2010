<?php
class TaskCleaner {
  public static function clean() {
    $runningTask = Db::getRow('SELECT id FROM task WHERE is_running = 1');
    if ($runningTask !== false) {
      Db::delete('task', 'id > ?', $runningTask['id']);
    }
    Db::update('task', array('is_running' => 0), $runningTask['id']);
  }
}