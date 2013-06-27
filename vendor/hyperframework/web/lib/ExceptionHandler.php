<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private $configPath;
    private $applicationClass;
    private $exception;

    public function __construct(
        $configPath = CONFIG_PATH, $applicationClass = 'Application'
    ) {
        $this->configPath = $configPath;
        $this->applicationClass = $applicationClass;
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
        $path = null;
        if (isset($config[$statusCode])) {
            $path = $config[$statusCode];
        }
        if ($path === null && isset($config['default'])) {
            $path = $config['default'];
        }
        if ($path !== null) {
            try {
                $application = new $this->applicationClass;
                $application->run($path);
            } catch (UnsupportedMediaTypeException $exception) {
            } catch (\Exception $exception) {
                trigger_error($exception, E_USER_ERROR);
            }
        }
    }
}
