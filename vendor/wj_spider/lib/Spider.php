<?php
class Spider {
  private $task;

  public function execute($tasks) {
    DbTask::initialize($tasks);
    while (($this->task = DbTask::getLastRow()) !== false) {
      $result = $this->dispatch();
      if ($result !== null) {
        DbTask::fail($result);
      }
      $this->show($result);
      DbTask::remove();
    }
  }

  private function dispatch($task) {
    $class = $task['type'].'Processor';
    $processor = new $class;
    return $processor->execute(eval('return '.$task['arguments'].';'));
  }

  private function show($result) {
    if ($result === null) {
      echo '.';
      return;
    }
    echo 'x';
  }
}