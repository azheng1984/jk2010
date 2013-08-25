<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private static $defaultErrorPath = 'error://internal_server_error';
    private static $exception;

    public static function run() {
        set_exception_handler(array(get_called_class(), 'handle'));
    }

    public static function handle($exception) {
        static::$exception = $exception;
        if (headers_sent()) {
            static::reportError($exception);
            return;
        }
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        static::resetOutput();
        $exception->sendHeader();
        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            try {
                static::displayError($exception->getCode());
            } catch (\Exception $recursiveException) {
                static::reportError(static::$exception, $recursiveException);
                return;
            }
        }
        if ($exception instanceof InternalServerErrorException) {
            static::reportError(static::$exception);
        }
    }

    public static function getException() {
        return static::$exception;
    }

    public function setDefaultErrorPath($value) {
        static::$defaultErrorPath = $value;
    }

    public static function reset() {
        static::$exception = null;
    }

    protected static function reportError(
        $exception, $recursiveException = null
    ) {
        if ($recursiveException !== null) {
            $message = 'Uncaught ' . $exception. PHP_EOL .
                PHP_EOL . 'Next ' . $recursiveException. PHP_EOL;
            trigger_error($message, E_USER_ERROR);
        }
        throw $exception;
    }

    protected static function resetOutput() {
        header_remove();
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    protected static function displayError($statusCode) {
        $_SERVER['PREVIOUS_REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        try {
            static::runErrorApplication($statusCode);
        } catch (UnsupportedMediaTypeException $recursiveException) {
        }
    }

    protected static function runErrorApplication($statusCode) {
        if (($path = static::getErrorPath($statusCode)) !== null) {
            Application::run($path, 'error');
        }
    }

    protected static function getErrorPath($statusCode) {
        $path = 'error://' .
            strtolower(str_replace(' ', '_', substr($statusCode, 4)));
        if (PathInfo::exists($path)) {
            return $path;
        }
        return static::$defaultErrorPath;
    }
}
