<?php
class RetryCommand {
  public function execute($merchant) {
    if (is_dir(CONFIG_PATH.'merchant/'.$merchant) === false) {
      echo 'no merchant "'.$merchant.'"'.PHP_EOL;
      exit;
    }
    DbConnection::connect($merchant);
    $GLOBALS['MERCHANT'] = $merchant;
    Lock::execute();
    TaskCleaner::clean();
    $this->restoreAllTasks();
  }

  private function restoreAllTasks() {
    foreach (Db::getAll('SELECT * FROM task_fail') as $task) {
      $this->restoreTask($task);
    }
  }

  private function restoreTask($task) {
    Db::insert('task', array(
      'id' => $task['task_id'],
      'processor' => $task['processor'],
      'argument_list' => $task['argument_list'],
    ));
    Db::delete('task_fail', 'task_id = ?', $task['task_id']);
    Db::delete('task_record', 'task_id = ?', $task['task_id']);
  }
}