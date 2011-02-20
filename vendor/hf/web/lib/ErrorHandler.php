<?php
class ErrorHandler {
  private $app;

  public function __construct($app) {
    $this->app = $app;
  }

  public function run() {
    set_exception_handler(array ($this, 'handle'));
  }

  public function stop() {
    restore_exception_handler();
  }

  public function handle($exception) {
    if (!headers_sent()) {
      $_ENV['error_handler'] = array (
        'exception' => $exception,
        'output_buffer' => ob_get_clean(),
      );
      $this->reload($exception);
    }
    trigger_error($exception, E_USER_ERROR);
  }

  private function reload($exception) {
    if (!$exception instanceof ApplicationException) {
      $exception = new InternalServerErrorException;
    }
    $status = substr($exception->getCode(), 0, 3);
    $config = require HF_CONFIG_PATH.'web'
                     .DIRECTORY_SEPARATOR.__CLASS__.'.config.php';
    if (isset($config[$status])) {
      $this->app->run($config[$status]);
    }
  }
}