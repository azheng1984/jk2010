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
            $this->report($exception);
        }
        $this->exception = $exception;
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        $exception->rewriteHeader();
        $config = require $this->configPath . 'error_handler.config.php';
        $statusCode = $exception->getCode();
        if (isset($config[$statusCode]) === false) {
            $this->report($this->exception);
        }
        try {
            $this->reload($config[$statusCode]);
        } catch (UnsupportedMediaTypeException $ignoredException) {
        } catch (\Exception $recursiveException) {
            $message = 'Uncaught ' . $this->exception . PHP_EOL .
                PHP_EOL . 'Next ' . $recursiveException . PHP_EOL;
            $this->report($message);
        }
        if ($exception instanceof InternalServerErrorException) {
            $this->report($this->exception);
        }
    }

    protected function report($data, $level = E_USER_ERROR) {
        if ($data instanceof \Exception && $level === E_USER_ERROR) {
            throw $data;
        }
        trigger_error($data, $level);
    }

    protected function reload($path) {
        $app = new Application;
        $app->run($path);
    }
}
