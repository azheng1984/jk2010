<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private $configPath;
    private $exception;

    public function __construct($configPath = CONFIG_PATH) {
        $this->configPath = $configPath;
    }

    public function run() {
        set_exception_handler(array($this, 'handle'));
    }

    public function getException() {
        return $this->exception;
    }

    public function handle($exception) {
        if (headers_sent()) {
            $this->reportError($exception);
        }
        $this->exception = $exception;
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        $exception->rewriteHeader();
        $config = require $this->configPath . 'error_handler.config.php';
        $statusCode = $exception->getCode();
        if (isset($config[$statusCode]) === false) {
            $this->reportError($this->exception);
        }
        try {
            $this->reload($config[$statusCode]);
        } catch (UnsupportedMediaTypeException $ignoredException) {
        } catch (\Exception $recursiveException) {
            $this->reportError($this->exception, $recursiveException);
        }
        if ($exception instanceof InternalServerErrorException) {
            $this->reportError($this->exception);
        }
    }

    protected function reportError($first, $second = null) {
        $message = $first;
        if ($second !== null) {
            $message = 'Uncaught ' . $first . PHP_EOL .
                PHP_EOL . 'Next ' . $second . PHP_EOL;
        }
        if ($message instanceof \Exception) {
            throw $message;
        }
        trigger_error($message, 'E_USER_ERROR');
    }

    protected function reload($path) {
        $app = new Application;
        $app->run($path);
    }
}
