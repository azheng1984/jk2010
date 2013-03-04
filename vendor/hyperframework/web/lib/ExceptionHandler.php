<?php
class ExceptionHandler {
  private $app;
  private $configPath;

  public function __construct($app, $configPath = CONFIG_PATH) {
    $this->app = $app;
    $this->configPath = $configPath;
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
    $this->reload($exception);
  }

  private function reload($exception) {
    $GLOBALS['EXCEPTION'] = $exception;
    if (!$exception instanceof ApplicationException) {
      $exception = new InternalServerErrorException;
    }
    $config = require $this->configPath.'error_handler.config.php';
    $statusCode = $exception->getCode();
    if (isset($config[$statusCode])) {
      $this->app->run($config[$statusCode]);
    }
  }
}