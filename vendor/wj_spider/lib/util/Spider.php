<?php
class Spider {
  public function __construct() {
    if (DbTask::isEmpty() && DbTaskRetry::isEmpty()) {
      foreach ($GLOBALS['task'] as $task) {
        DbTask::insert($task['type'], $task['arguments']);
      }
      return;
    }
    $runningTask = DbTask::getRunning();
    if ($runningTask !== false) {
      DbTask::deleteByLargerThanId($runningTask['id']);
    }
  }

  public function run() {
    while (($task = DbTask::getLastRow()) !== false) {
      DbTask::setRunning($task['id']);
      $result = $this->dispatch($task);
      $status = '.';
      if ($result !== null) {
        $this->fail($task, $result);
        $status = '*';
      }
      if ($result === null && $task['is_retry']) {
        DbTaskRetryRecord::removeByTaskId($task['id']);
      }
      DbTask::remove($task['id']);
      echo $status;
    }
  }

  private function dispatch($task) {
    $class = $task['type'].'Processor';
    $processor = new $class;
    return $processor->execute(eval('return '.$task['arguments'].';'));
  }

  private function fail($task, $result) {
    DbTaskRetry::insert($task);
    DbTaskRetryRecord::insert($task['id'], $result);
  }
}