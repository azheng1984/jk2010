<?php
class SpiderCommand {
  private $isRetry = false;
  private $taskId;

  public function __construct($options) {
    if (isset($options['retry'])) {
      $this->isRetry = true;
    }
    if (isset($options['task_id'])) {
      $this->taskId = $options['task_id'];
    }
  }

  public function execute() {
    if (Lock::execute() === false) {
      echo '[locked]'.PHP_EOL;
      return;
    }
    $isRetry = false;
    $taskId = null;
    $spider = new Spider;
    $spider->execute($GLOBALS['tasks']);
  }
}