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
    $this->task = DbTask::get($id);
    $this->isRetry = $this->task['is_retry'] === '1';
    if ($this->task === false) {
      $this->task = DbTaskRetry::getByTaskId($id);
      $this->isRetry = true;
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
    foreach (DbTaskRecord::getByTaskId($this->id) as $record) {
      $result .= 'time:'.$record['time'].PHP_EOL;
      $result .= 'result:'.PHP_EOL;
      $result .= $record['result'];
      $result .= PHP_EOL.'---------------------------------'.PHP_EOL;
    }
    return $result;
  }
}