<?php
class ShowCommand {
  private $isExportToFile;
  private $isRetry;
  private $id;
  private $task;

  public function __construct($options) {
    $this->isExportToFile = isset($options['export_to_file']);
  }

  public function execute($id) {
    $this->id = $id;
    $this->task = Db::getRow('SELECT * FROM task WHERE id = ?', $id);
    $this->isRetry = $this->task['is_retry'] === '1';
    if ($this->task === false) {
      $this->task = Db::getRow(
        'SELECT * FROM task_retry WHERE task_id = ?', $id
      );
      $this->isRetry = true;
    }
    if ($this->task === false) {
      fwrite(STDERR, 'fail:no task'.PHP_EOL);
      return;
    }
    if ($this->isExportToFile) {
      $this->export();
      return;
    }
    $this->show();
  }

  private function export() {
    file_put_contents('task_'.$this->id.'.txt', $this->getContent());
  }

  private function show() {
    echo $this->getContent();
  }

  private function getContent() {
    $result = 'id:'.$this->id.PHP_EOL;
    $result .= 'type:'.$this->task['type'].PHP_EOL;
    $result .= 'arguments:'.$this->task['arguments'].PHP_EOL;
    if ($this->isRetry === true) {
      $result .= $this->getRecords();
    }
    return $result;
  }

  private function getRecords() {
    $result = '[records]'.PHP_EOL;
    $recordList =
      Db::getAll('SELECT * FROM task_record WHERE task_id = ?', $this->id);
    foreach ($recordList as $record) {
      $result .= 'time:'.$record['time'].PHP_EOL;
      $result .= 'result:'.PHP_EOL;
      $result .= $record['result'];
      $result .= PHP_EOL.'---------------------------------'.PHP_EOL;
    }
    return $result;
  }
}