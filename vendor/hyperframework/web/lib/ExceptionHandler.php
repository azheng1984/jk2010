<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private $exception;
    private $previousRequestMethod;

    public function run() {
        set_exception_handler(array($this, 'handle'));
    }

    public function handle($exception) {
        if (headers_sent()) {
            $this->reportError($exception);
            return;
        }
        $this->exception = $exception;
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        $exception->rewriteHeader();
        $statusCode = $exception->getCode();
        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            $this->previousRequestMethod = $_SERVER['REQUEST_METHOD'];
            $_SERVER['REQUEST_METHOD'] = 'GET';
            try {
                $this->reload($this->getErrorPath($statusCode));
            } catch (NotFoundException $recursiveException) {
            } catch (UnsupportedMediaTypeException $recursiveException) {
            } catch (\Exception $recursiveException) {
                $this->reportError($this->exception, $recursiveException);
                return;
            }
        }
        if ($exception instanceof InternalServerErrorException) {
            $this->reportError($this->exception);
        }
    }

    public function getException() {
        return $this->exception;
    }

    public function getPreviousRequestMethod() {
        return $this->previousRequestMethod;
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
        $app = new Application;
        $app->run($path);
    }

    protected function getErrorPath($statusCode) {
        return 'error://' .
            strtolower(str_replace(' ', '_', substr($statusCode, 4))); 
    }
}
