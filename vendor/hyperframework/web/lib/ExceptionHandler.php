<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private $applicationCachePath;
    private $configPath;
    private $configProvider;
    private $exception;

    public function __construct(
        $configPath = null, $configProvider = null, $applicationCachePath = null
    ) {
        $this->configPath = $configPath;
        $this->configProvider = $configProvider;
        $this->applicationCachePath = $applicationCachePath;
    }

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
        $config = $this->getConfig();
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

    protected function reportError($first, $second = null) {
        $message = $first;
        if ($second !== null) {
            $message = 'Uncaught ' . $first . PHP_EOL .
                PHP_EOL . 'Next ' . $second . PHP_EOL;
        }
        if ($message instanceof \Exception) {
            throw $message;
        }
        trigger_error($message, E_USER_ERROR);
    }

    protected function reload($path) {
        $app = new Application($this->getApplicationCachePath());
        $app->run($path);
    }

    protected function getApplicationCachePath() {
        if ($this->applicationCachePath === null) {
            return CACHE_PATH . 'application.error.cache.php';
        }
        return $this->applicationCachePath;
    }

    private function getConfig() {
        $path = $this->configPath === null ?
            CONFIG_PATH . 'exception_handler.config.php' : $this->configPath;
        if ($this->configProvider === null) {
            return require $path;
        }
        return $this->configProvider->get($path);
    }
}
