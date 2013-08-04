<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private $configPath;
    private $appClass;
    private $exception;

    public function __construct(
        $configPath = CONFIG_PATH, $appClass = 'Hyperframework\Web\Application'
    ) {
        $this->configPath = $configPath;
        $this->appClass = $appClass;
    }

    public function run() {
        set_exception_handler(array($this, 'handle'));
    }

    public function getException() {
        return $this->exception;
    }

    public function handle($exception) {
        if (headers_sent()) {
            throw $exception;
        }
        $this->exception = $exception;
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        $exception->rewriteHeader();
        $config = require $this->configPath . 'error_handler.config.php';
        $statusCode = $exception->getCode();
        if (isset($config[$statusCode]) === false) {
            throw $this->exception;
        }
        $hasError = $exception instanceof InternalServerErrorException;
        try {
            $app = new $this->appClass;
            $app->run($config[$statusCode]);
        } catch (\Exception $recursiveException) {
            $hasRecursiveError =
                $recursiveException instanceof ApplicationException === false ||
                $recursiveException instanceof InternalServerErrorException;
            if ($hasError === false && $hasRecursiveError) {
                throw $recursiveException;
            }
            if ($hasError && $hasRecursiveError) {
                $message = 'Uncaught ' . $this->exception . PHP_EOL .
                    PHP_EOL . 'Next ' . $recursiveException . PHP_EOL;
                trigger_error($message, E_USER_ERROR);
            }
        }
        if ($hasError) {
            throw $this->exception;
        }
    }
}
