<?php
class RetryCommand {
  public function execute($id = null) {
    if ($id !== null) {
      $task = DbTaskRetry::get($id);
      $this->moveTask($task);
      return;
    }
    foreach (DbTaskRetry::getAll() as $task) {
      $this->restoreTask($task);
    }
  }

  private function restoreTask($task) {
    DbTask::reinsert(
      $task['id'], $task['type'], $task['arguments']
    );
    DbTaskRetry::delete($task['id']);
  }
}