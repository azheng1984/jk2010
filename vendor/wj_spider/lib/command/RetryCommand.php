<?php
class RetryCommand {
  public function execute($id = null) {
    Lock::execute();
    if ($id === null) {
      $this->restoreAllTasks();
      return;
    }
    $this->restoreTaskByID($id);
  }

  private function restoreTaskById($id) {
    $task = DbTaskRetry::getByTaskId($id);
    if ($task === false) {
      return;
    }
    $this->restoreTask($task);
  }

  private function restoreAllTasks() {
    foreach (DbTaskRetry::getAll() as $task) {
      $this->restoreTask($task);
    }
  }

  private function restoreTask($task) {
    DbTask::reinsert(
      $task['task_id'], $task['type'], $task['arguments']
    );
    DbTaskRetry::deleteByTaskId($task['task_id']);
  }
}