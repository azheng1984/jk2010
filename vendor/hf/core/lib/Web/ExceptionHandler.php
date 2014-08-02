<?php
namespace Hyperframework\Web;

class ExceptionHandler {
    private static $exception;

    final public static function run() {
        set_exception_handler(array(get_called_class(), 'handle'));
    }

    final public static function handle($exception) {
        if (headers_sent()) {
            static::triggerError($exception);
            return;
        }
        self::$exception = $exception;
        if ($exception instanceof HttpException === false) {
            $exception = new InternalServerErrorException;
        }
        static::resetOutput();
        $exception->setHeader();
        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            try {
                static::displayError($exception);
            } catch (\Exception $e) {
                static::triggerError(self::$exception, $e);
            }
        }
        if ($exception instanceof InternalServerErrorException) {
            static::triggerError(self::$exception);
        }
    }

    protected static function getException() {
        return self::$exception;
    }

    public static function reset() {
        self::$exception = null;
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
        $obLevel = ob_get_level();
        while ($obLevel > 0) {
            ob_end_clean();
            --$obLevel;
        }
    }

    protected static function displayError($exception) {
        try {
            ViewDispatcher::run(
                PathInfo::get('/', 'ErrorApp'), $exception
            );
        } catch (NotFoundException $e) {
        } catch (NotAcceptableException $e) {}
    }
}
