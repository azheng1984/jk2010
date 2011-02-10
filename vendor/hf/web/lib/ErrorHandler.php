<?php
class ErrorHandler {
  private $app;
  private static $exception;
  private static $previousOutputBuffer;

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
      self::$exception = $exception;
      self::$previousOutputBuffer = ob_get_clean();
      $this->reload();
    }
    trigger_error($exception, E_USER_ERROR);
  }

  private function reload() {
    $status = '500';
    if (self::$exception instanceof ApplicationException) {
      $status = substr(self::$exception->getCode(), 0, 3);
    }
    $configPath = HF_CONFIG_PATH.'web'
                 .DIRECTORY_SEPARATOR.__CLASS__.'.config.php';
    $config = require $configPath;
    if (isset($config[$status])) {
      $this->app->run($config[$status]);
    }
  }

  public static function getException() {
    return self::$exception;
  }

  public static function getPreviousOutputBuffer() {
    return self::$previousOutputBuffer;
  }

  public static function reset() {
    self::$exception = null;
    self::$previousOutputBuffer = null;
  }
}