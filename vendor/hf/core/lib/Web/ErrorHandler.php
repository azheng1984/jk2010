<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\Web\Html\ErrorPage;

class ErrorHandler {
    private static $exception;
    private static $log;
    private static $isOutputBufferEnabled;
    private static $isDebugEnabled;
    private static $hasError;

    private $levels = array(
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'User Deprecated',
        E_NOTICE            => 'Notice',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
        E_WARNING           => 'Warning',
        E_USER_WARNING      => 'User Warning',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_CORE_WARNING      => 'Core Warning',
        E_USER_ERROR        => 'User Error',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        E_COMPILE_ERROR     => 'Compile Error',
        E_PARSE             => 'Parse Error',
        E_ERROR             => 'Error',
        E_CORE_ERROR        => 'Core Error'
    );

    final public static function run() {
        if (ini_get('display_errors') === '1') {
            ini_set('display_errors', false);
            self::$isDebugEnabled = true;
            Config::get('hyperframework.web.error_handler.debug.format');
            //html for text
            if (Config::get('hyperframework.web.error_handler.debug.enable_output_buffer') !== false) {
                if (ob_get_level() === 0 || headers_sent() === false) {
                    ob_start();
                    self::$isOutputBufferEnabled = true;
                }
            }
        }
        $class = get_called_class();
        set_error_handler(array($class, 'handleError'), E_ALL);
        set_exception_handler(array($class, 'handleException'));
        register_shutdown_function(array($class, 'handleFatalError'));
    }

    final public static function handleError($type, $message, $file, $line) {
        if (self::$hasError) {
            return;
        }
        if (error_reporting() & $type) {
            if (self::$isDebugEnabled) {
                self::$hasError = true;
                ini_set('display_errors', true);
            }
            self::$exception = new \ErrorException(
                $message, 0, $type, $file, $line
            );
            self::handleException($exception);
        }
    }

    final public static function handleFatalError() {
        $error = error_get_last();
        if ($error === null) {
            return;
        }
        $fatalErrorTypes = array(
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_CORE_WARNING,
            E_COMPILE_ERROR,
            E_COMPILE_WARNING
        );
        if (in_array($error['type'], $fatalErrorTypes)) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    final public static function handleException($exception) {
        self::$exception = $exception;
        if ($exception instanceof HttpException === false) {
            $exception = new InternalServerErrorException;
        }
        if (headers_sent()) {
            if (self::$isDebugEnabled !== true) {
                self::writeExceptionLog($exception);
                exit(1);
            }
        } else {
            self::$headers = headers_list();
            header_remove();
            $exception->setHeader();
        }
        if (self::$isDebugEnabled !== true) {
            static::cleanOutputBuffer();
        } elseif (self::$isOutpuBufferEnabled) {
            self::$output = static::getOutputBuffer();
        } else {
            static::flushOutputBuffer();
        }
        if (self::$isDebugEnabled) {
            if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
                static::renderCustomErrorPage(self::$exception);
            }
        } else {
            ErrorPage::renderException(self::$exception);
        }
        exit(1);
    }

    protected static function cleanOutputBuffer() {
        $obLevel = ob_get_level();
        while ($obLevel > 0) {
            ob_end_clean();
            --$obLevel;
        }
    }

    protected static function getOutputBuffer() {
        $obLevel = ob_get_level();
        if ($obLevel < 1) {
            //display output buffer error!!!
            return;
        }
        while ($obLevel > 1) {
            ob_end_clean();
            --$obLevel;
        }
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    protected static function flushOutputBuffer() {
        $obLevel = ob_get_level();
        while ($obLevel > 0) {
            ob_end_flush();
            --$obLevel;
        }
    }

//    private static function renderErrorPage($exception) {
//        $class = get_called_class();
//        if (self::$isDebugEnabled) {
//            $outputBuffer = self::flushOutputBuffer();
//            ErrorPage::renderError($error, self::$outputBuffer);
//            return;
//        }
//        self::resetOutput();
//        self::renderCustomErrorPage($exception);
//    }
//
//    protected static function generateErrorLogMessage(
//        $error, $recursiveError = null
//    ) {
//        error_log($message);
//    }
//
//    protected static function generateExceptionLogMessage(
//        $exception, $recursiveException = null
//    ) {
//        if ($recursiveException !== null) {
//            $message = 'Uncaught ' . $exception . PHP_EOL
//                . PHP_EOL . 'Next ' . $recursiveException . PHP_EOL;
//            return;
//        }
//        throw $exception;
//    }
//
//    protected static function writeLog($message) {
//        error_log($message);
//    }
//
//
//    protected static function getException() {
//        return self::$exception;
//    }
//
//    public static function reset() {
//        self::$exception = null;
//    }
//
//    protected static function triggerError(
//        $exception, $recursiveException = null
//    ) {
//        if ($recursiveException !== null) {
//            $message = 'Uncaught ' . $exception . PHP_EOL
//                . PHP_EOL . 'Next ' . $recursiveException . PHP_EOL;
//            trigger_error($message, E_USER_ERROR);
//        }
//        throw $exception;
//    }
//
//    protected static function resetOutput() {
//        header_remove();
//        $obLevel = ob_get_level();
//        while ($obLevel > 0) {
//            ob_end_clean();
//            --$obLevel;
//        }
//    }
//
//    protected static function renderCustomErrorPage($exception) {
//        try {
//            ViewDispatcher::run(
//                PathInfo::get('/', 'ErrorApp'), $exception
//            );
//        } catch (NotFoundException $e) {
//        } catch (NotAcceptableException $e) {
//        }
//    }
}
