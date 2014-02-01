<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private static $exception;

    public static function run() {
        set_exception_handler(array(get_called_class(), 'handle'));
    }

    public static function handle($exception) {
        if (headers_sent()) {
            static::reportError($exception);
            return;
        }
        static::$exception = $exception;
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        static::resetOutput();
        $exception->setHeader();
        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            try {
                static::displayError($exception);
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

    public static function reset() {
        restore_exception_handler();
    }

    protected static function reportError(
        $exception, $recursiveException = null
    ) {
        if ($recursiveException !== null) {
            $message = 'Uncaught ' . $exception . PHP_EOL
                . PHP_EOL . 'Next ' . $recursiveException . PHP_EOL;
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

    protected static function displayError($applicationException) {
        ErrorApplication::run($applicationException->getCode());
    }
}
