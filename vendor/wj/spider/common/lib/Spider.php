<?php
class Spider {
  public function run() {
    while (($task = DbTask::getLastRow()) !== false) {
      DbTask::setRunning($task['id']);
      try {
        $this->dispatch($task);
        if ($task['is_retry']) {
          DbTaskRecord::deleteByTaskId($task['id']);
        }
        DbTask::remove($task['id']);
        echo '.';
      } catch (Exception $ex) {
        $this->fail($task, $ex);
        echo 'x';
      }
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