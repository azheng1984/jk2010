<?php
class TaskRetryCommand {
  public function execute($id = null) {
    if ($id !== null) {
      $task = DbTaskRetry::get($id);
      $this->moveTask($task);
      return;
    }
    foreach (DbTaskRetry::getAll() as $task) {
      $this->moveTask($task);
    }
  }

  private function moveTask($task) {
    DbTask::reinsert(
      $task['id'], $task['type'], $task['arguments']
    );
    DbTaskRetry::delete($task['id']);
  }
}