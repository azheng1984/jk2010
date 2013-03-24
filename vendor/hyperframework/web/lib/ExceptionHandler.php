<?php
class ExceptionHandler {
  private $appClass;
  private $config;
  private $exception;

  public function __construct($config = null, $appClass = 'Application') {
    $this->appClass = $appClass;
    $this->config = $config;
  }

  public function run() {
    set_exception_handler(array($this, 'handle'));
  }

  public function stop() {
    restore_exception_handler();
  }

  public function handle($exception) {
    if (headers_sent()) {
      trigger_error($exception, E_USER_ERROR);
    }
    header_remove();
    $this->reload($exception);
  }

  public function getException() {
    return $this->exception;
  }

  private function reload($exception) {
    $this->$exception = $exception;
    if (!$exception instanceof ApplicationException) {
      $exception = new InternalServerErrorException;
    }
    if ($this->config === null) {
      $this->config = require CONFIG_PATH.'error_handler.config.php';
    }
    $statusCode = $exception->getCode();
    if (isset($this->config[$statusCode])) {
      $app = new $this->appClass;
      $app->run($this->config[$statusCode]);
    }
  }
}