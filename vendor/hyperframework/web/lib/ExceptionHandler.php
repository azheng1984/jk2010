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
            trigger_error($exception, E_USER_ERROR);
        }
        $this->exception = $exception;
        $this->reload($exception);
    }

    public function stop() {
        restore_exception_handler();
    }

    private function reload($exception) {
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        $config = require $this->configPath . 'error_handler.config.php';
        $exception->rewriteHeader();
        $statusCode = $exception->getCode();
        if (isset($config[$statusCode])) {
            try {
                $app = new $this->appClass;
                $app->run($config[$statusCode]);
            } catch (UnsupportedMediaTypeException $exception) {
            } catch (\Exception $exception) {
                trigger_error($exception, E_USER_ERROR);
            }
        }
    }
}
