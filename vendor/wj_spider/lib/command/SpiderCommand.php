<?php
class SpiderCommand {
  private $isRetry = false;
  private $retryTaskId;

  public function __construct($options) {
    if (isset($options['retry'])) {
      $this->isRetry = true;
    }
    if (isset($options['retry_task_id'])) {
      $this->retryTaskId = $options['retry_task_id'];
    }
  }

  public function execute() {
    if (Lock::execute() === false) {
      echo '[locked]'.PHP_EOL;
      return;
    }
    $isRetry = false;
    $retryTaskId = null;
    global $tasks;
    $spider = new Spider;
    $spider->execute($tasks);
  }
}