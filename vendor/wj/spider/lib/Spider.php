<?php
class Spider {
  public function run() {
    for (;;) {
      $task = Db::getRow('SELECT * FROM task ORDER BY id DESC LIMIT 1');
      if ($task === false) {
        break;
      }
      Db::update('task', array('is_running' => '1'), 'id = ?', $task['id']);
      try {
        $this->dispatch($task);
        if ($task['is_retry']) {
          Db::delete('task_record', 'task_id = ?', $task['id']);
        }
        Db::delete('task', 'id = ?', $task['id']);
        echo '.';
      } catch (Exception $exception) {
        $this->fail($task, $exception);
        echo 'x';
      }
    }
  }

  private function dispatch($task) {
    $class = $task['type'].'Processor';
    $processor = new $class;
    return $processor->execute(eval('return '.$task['arguments'].';'));
  }

  private function fail($task, $exception) {
    Db::execute(
      'REPLACE INTO task_retry(task_id, type, arguments) VALUES(?, ?, ?)',
      $task['id'], $task['type'], $task['arguments']
    );
    Db::execute('INSERT INTO task_record(task_id, result, time)'
      .' VALUES(?, ?, NOW())', $task['id'], var_export($exception, true));
  }
}