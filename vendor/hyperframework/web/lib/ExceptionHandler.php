<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private $exception;

    public function run() {
        set_exception_handler(array($this, 'handle'));
    }

    public function handle($exception) {
        $this->exception = $exception;
        if (headers_sent()) {
            $this->reportError($exception);
            return;
        }
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        $this->cleanOutput();
        $exception->sendHeader();
        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            try {
                $this->displayError($exception->getCode());
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

    protected function reportError($exception, $recursiveException = null) {
        if ($recursiveException !== null) {
            $message = 'Uncaught ' . $exception. PHP_EOL .
                PHP_EOL . 'Next ' . $recursiveException. PHP_EOL;
            trigger_error($message, E_USER_ERROR);
        }
        throw $exception;
    }

    protected function cleanOutput() {
        header_remove();
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    protected function displayError($statusCode) {
        $_SERVER['PREVIOUS_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        try {
            $path = 'error://' .
                strtolower(str_replace(' ', '_', substr($statusCode, 4)));
            $app = new Application;
            $app->run($path);
        } catch (NotFoundException $recursiveException) {
        } catch (UnsupportedMediaTypeException $recursiveException) {
        }
    }
}
