<?php
class Spider {
  public function run() {
    for (;;) {
      $task = Db::getRow('SELECT * FROM task ORDER BY id DESC LIMIT 1');
      if ($task === false) {
        break;
      }
      Db::update('task', array('is_running' => 1), 'id = ?', $task['id']);
      try {
        $this->dispatch($task);
        Db::delete('task', 'id = ?', $task['id']);
        echo '.';
      } catch (Exception $exception) {
        $this->fail($task, $exception);
        echo 'x';
      }
    }
  }

  private function dispatch($task) {
    $class = $task['processor'].'Processor';
    $processor = new $class;
    $argumentList = eval('return '.$task['argument_list'].';');
    return call_user_func_array(array($processor, 'execute'), $argumentList);
  }

  private function fail($task, $exception) {
    Db::execute(
      'REPLACE INTO task_retry(task_id, processor, argument_list)'
        .' VALUES(?, ?, ?)',
      $task['id'], $task['processor'], $task['argument_list']
    );
    Db::execute('INSERT INTO task_record(task_id, result, time)'
      .' VALUES(?, ?, NOW())', $task['id'], var_export($exception, true));
    Db::delete('task', 'id = ?', $task['id']);
  }
}