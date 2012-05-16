<?php
class ShowCommand {
  private $isExportToFile;
  private $isRetry = false;
  private $id;
  private $task;

  public function __construct($options) {
    $this->isExportToFile = isset($options['export_to_file']);
  }

  public function execute($merchant, $id) {
    if (is_dir(CONFIG_PATH.'merchant/'.$merchant) === false) {
      echo 'no merchant "'.$merchant.'"'.PHP_EOL;
      exit;
    }
    DbConnection::connect($merchant);
    $this->id = $id;
    $this->task = Db::getRow('SELECT * FROM task WHERE id = ?', $id);
    if ($this->task === false) {
      $this->task = Db::getRow(
        'SELECT * FROM task_fail WHERE task_id = ?', $id
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
    $result .= 'processor:'.$this->task['processor'].PHP_EOL;
    $result .= 'argument_list:'.$this->task['argument_list'].PHP_EOL;
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
      $result .= $record['exception'];
      $result .= PHP_EOL.'---------------------------------'.PHP_EOL;
    }
    return $result;
  }
}