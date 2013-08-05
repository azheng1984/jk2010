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
            $this->triggerError($exception);
        }
        $this->exception = $exception;
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        $exception->rewriteHeader();
        $config = require $this->configPath . 'error_handler.config.php';
        $statusCode = $exception->getCode();
        if (isset($config[$statusCode]) === false) {
            $this->triggerError($this->exception);
        }
        try {
            $app = new $this->appClass;
            $app->run($config[$statusCode]);
        } catch (UnsupportedMediaTypeException $ignoredException) {
        } catch (\Exception $recursiveException) {
            $message = 'Uncaught ' . $this->exception . PHP_EOL .
                PHP_EOL . 'Next ' . $recursiveException . PHP_EOL;
            $this->triggerError($message);
        }
        if ($exception instanceof InternalServerErrorException) {
            $this->triggerError($this->exception);
        }
    }

    protected function triggerError($source, $level = E_USER_ERROR) {
        if ($source instanceof \Exception && $level === E_USER_ERROR) {
            throw $source;
        }
        trigger_error($source, $level);
    }
}
