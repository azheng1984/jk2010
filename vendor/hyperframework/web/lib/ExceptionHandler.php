<?php
class ExceptionHandler {
  private $app;

  public function __construct($app) {
    $this->app = $app;
  }

  public function run() {
    set_exception_handler(array($this, 'handle'));
  }

  public function stop() {
    restore_exception_handler();
  }

  public function handle($exception) {
    if (!headers_sent()) {
      $this->reload($exception);
    }
    trigger_error($exception, E_USER_ERROR);
  }

  private function reload($exception) {
    if ($exception instanceof ApplicationException === false) {
      $exception = new InternalServerErrorException;
    }
    $config = require CONFIG_PATH.'error_handler.config.php';
    $statusCode = $exception->getCode();
    if (isset($config[$statusCode])) {
      $this->app->run($config[$statusCode]);
    }
  }
}