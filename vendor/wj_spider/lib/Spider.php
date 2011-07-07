<?php
class Spider {
  public function execute($tasks) {
    DbTask::initialize($tasks);
    while (($task = DbTask::getLastRow()) !== false) {
      DbTask::setRunning($task['id']);
      if (($result = $this->dispatch($task)) !== null) {
        DbTaskRetry::insert($task);
        DbTaskRetryHistory::insert($result);
      }
      DbTask::remove($task);
      echo $result === null ? '.' : 'x';
    }
  }

  private function dispatch($task) {
    $class = $task['type'].'Processor';
    $processor = new $class;
    return $processor->execute(eval('return '.$task['arguments'].';'));
  }
}