<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private $configDirectory;
    private $configProvider;
    private $exception;

    public function run() {
        set_exception_handler(array($this, 'handle'));
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

    public function getException() {
        return $this->exception;
    }

    public function setConfigDirectory($value) {
        $this->configDirectory = $value;
    }

    public function setConfigProvider($value) {
       $this->configProvider = $value; 
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

    private function getConfig() {
        $configPath = ($this->configDirectory === null ?
            CONFIG_PATH : $this->configDirectory) . 'error_handler.config.php';
        if ($this->configProvider === null) {
            return require $configPath;
        }
        return $this->configProvider->get($configPath);
    }
}
