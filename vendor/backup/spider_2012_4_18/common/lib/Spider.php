<?php
class Spider {
  public function run() {
    while (($task = DbTask::getLastRow()) !== false) {
      DbTask::setRunning($task['id']);
      $result = $this->dispatch($task);
      $status = '.';
      if ($result !== null) {
        $this->fail($task, $result);
        $status = 'x';
      }
      if ($result === null && $task['is_retry']) {
        DbTaskRecord::deleteByTaskId($task['id']);
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
    DbTaskRecord::insert($task['id'], $result);
  }
}