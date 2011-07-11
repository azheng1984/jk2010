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

  public function execute() {
    while (($task = DbTask::getLastRow()) !== false) {
      DbTask::setRunning($task['id']);
      $result = $this->dispatch($task);
      $status = true;
      if ($result !== null) {
        $this->fail($task, $result);
        $status = false;
      }
      if ($result === null && $task['is_retry']) {
        DbTaskRetryHistory::removeByTaskId($task['id']);
      }
      DbTask::remove($task['id']);
      echo result;
    }
  }

  private function dispatch($task) {
    $class = $task['type'].'Processor';
    $processor = new $class;
    return $processor->execute(eval('return '.$task['arguments'].';'));
  }

  private function fail($task, $result) {
    DbTaskRetry::insert($task);
    DbTaskRetryHistory::insert($task['id'], $result);
  }
}