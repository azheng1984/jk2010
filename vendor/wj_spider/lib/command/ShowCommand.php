<?php
class ShowCommand {
  private $isExportToFile;
  private $task;

  public function __construct($options) {
    $this->isExportToFile = isset($options['export_to_file']);
  }

  public function execute($id) {
    $this->task = DbTask::get($id);
    if ($this->task === false) {
      $this->task = DbTaskRetry::getByTaskId($id);
      $this->task['is_retry'] = true;
    }
    if ($this->task === false) {
      echo 'no record';
      return;
    }
    if ($this->isExportToFile) {
      $this->export();
      return;
    }
    $this->show();
  }

  private function exprot() {
    file_put_contents('task_'.$this->task['id'].'.txt', $this->getContent());
  }

  private function show() {
    echo $this->getContent();
  }

  private function getContent() {
    $result = 'id:'.$this->task['id'].PHP_EOL;
    $result .= 'type:'.$this->task['type'].PHP_EOL;
    $result .= 'arguments:'.var_export($this->task['arguments'], true).PHP_EOL;
    if ($this->task['is_retry'] === true) {
      $result .= $this->getRecords();
    }
    return $result;
  }

  private function getRecords() {
    $result = 'records:'.PHP_EOL;
    foreach (DbTaskRetryRecord::getByTaskId($this->task['id']) as $record) {
      $result .= 'time:'.$record['time'].PHP_EOL;
      $result .= 'result:'.PHP_EOL;
      $result .= var_export($record['result'], true);
      $result .= PHP_EOL.'---------------------------------'.PHP_EOL;
    }
  }
}