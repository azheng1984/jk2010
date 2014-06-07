<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private static $exception;
    private static $statusCode;

    final public static function run() {
        set_exception_handler(array(get_called_class(), 'handle'));
    }

    final public static function handle($exception) {
        if (headers_sent()) {
            static::triggerError($exception);
            return;
        }
        self::$exception = $exception;
        if ($exception instanceof ApplicationException === false) {
            $exception = new InternalServerErrorException;
        }
        self::$statusCode = $exception->getCode();
        static::resetOutput();
        $exception->setHeader();
        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            try {
                static::displayError();
            } catch (\Exception $recursiveException) {
                static::triggerError(self::$exception, $recursiveException);
            }
        }
        if ($exception instanceof InternalServerErrorException) {
            static::triggerError(self:$exception);
        }
    }

    final public static function getException() {
        return self::$exception;
    }

    final public static function getStatusCode() {
        return self::$statusCode;
    }

    public static function reset() {
        restore_exception_handler();
        self::$exception = null;
        self::$statusCode = null;
    }

    protected static function triggerError(
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
        $level = ob_get_level();
        while ($level > 0) {
            ob_end_clean();
            --$level;
        }
    }

    protected static function displayError() {
        $pathInfo = PathInfo::get('#ErrorApp');
        try {
            ViewDispatcher::run($pathInfo);
        } catch (NotAcceptableException $ignoredException) {}
    }
}
