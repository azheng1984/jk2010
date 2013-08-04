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
        $this->reload($exception);
    }

    private function reload($exception) {
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException(null, $exception);
        }
        $config = require $this->configPath . 'error_handler.config.php';
        $exception->rewriteHeader();
        $statusCode = $exception->getCode();
        if (isset($config[$statusCode]) === false) {
            throw $this->getException();
        }
        try {
            $app = new $this->appClass;
            $app->run($config[$statusCode]);
        } catch (UnsupportedMediaTypeException $exception) {
        } catch (\Exception $exception) {
            $message = 'Uncaught ' . $this->getException() . PHP_EOL .
                PHP_EOL . 'Next ' . $exception . PHP_EOL;
            trigger_error($message, E_USER_ERROR);
        }
    }
}
