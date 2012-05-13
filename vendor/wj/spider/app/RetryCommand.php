<?php
class RetryCommand {
  public function execute($merchant, $id = null) {
    Lock::execute();
    TaskCleaner::clean();
    if ($id === null) {
      $this->restoreAllTasks();
      return;
    }
    $this->restoreTaskByID($id);
  }

  private function restoreTaskById($id) {
    $task = Db::getRow('SELECT * FROM task_retry WHERE task_id = ?', $id);
    if ($task === false) {
      return;
    }
    $this->restoreTask($task);
  }

  private function restoreAllTasks() {
    foreach (Db::getAll('SELECT * FROM task_retry') as $task) {
      $this->restoreTask($task);
    }
  }

  private function restoreTask($task) {
    Db::insert(
    'task',
      array(
	      'id' => $task['task_id'],
	      'type' => $task['type'],
	      'arguments' => $task['arguments'],
	      'is_retry' => 1
      )
    );
    Db::delete('task_retry', 'task_id = ?', $task['task_id']);
  }
}