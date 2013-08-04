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
            $exception = new InternalServerErrorException(null, $exception);
        }
        $config = require $this->configPath . 'error_handler.config.php';
        $exception->rewriteHeader();
        $statusCode = $exception->getCode();
        if (isset($config[$statusCode]) === false) {
            throw $this->exception;
        }
        try {
            $app = new $this->appClass;
            $app->run($config[$statusCode]);
        } catch (\Exception $nextException) {
            if ($nextException instanceof InternalServerErrorException ||
                $nextException instanceof ApplicationException === false ||
                $exception instanceof InternalServerErrorException) {
                $message = 'Uncaught ' . $this->exception . PHP_EOL;
                    . PHP_EOL . 'Next ' . $nextException . PHP_EOL;
                trigger_error($message, E_USER_ERROR);
            }
            return;
        }
        if ($exception instanceof InternalServerErrorException) {
            throw $this->exception;
        }
    }
}
