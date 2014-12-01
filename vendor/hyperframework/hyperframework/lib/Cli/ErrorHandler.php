<?php
namespace Hyperframework\Cli;

use Exception;
use ErrorException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ErrorCodeHelper;
use Hyperframework\Logging\Logger;
use Hyperframework\Web\Html\DebugPage;

class ErrorHandler {
    private static $exception;
    private static $isError;
    private static $shouldExit;
    private static $exitLevel;
    private static $isDebugEnabled;
    private static $outputBufferLevel;
    private static $errorReporting;
    private static $ignoredErrors;

    final public static function run() {
        self::$isDebugEnabled = ini_get('display_errors') === '1';
        self::$errorReporting = error_reporting();
        $class = get_called_class();
//        set_error_handler(array($class, 'handleError'), self::$errorReporting);
//        set_exception_handler(array($class, 'handleException'));
//        register_shutdown_function(array($class, 'handleFatalError'));
//        if (self::$isDebugEnabled) {
//            ob_start();
//            self::$outputBufferLevel = ob_get_level();
//        }
//        self::disableErrorReporting();
    }

    final public static function handleException($exception, $isError = false) {
        if (self::$exception !== null) {
            if ($isError) {//fatal error only
                return false;
            }
            throw $exception;
        }
        error_reporting(self::$errorReporting);
        self::$exception = $exception;
        self::$isError = $isError;
        if ($isError) {
            $exitLevel = E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED;
            self::$shouldExit = (self::$exception->getSeverity() & $exitLevel)
                !== 0;
        } else {
            self::$shouldExit = true;
        }
        if (self::$isDebugEnabled) {
            if ($isError) {
                echo PHP_EOL, self::getDefaultErrorLog(), PHP_EOL;
            } else {
                echo PHP_EOL, self::getDefaultExceptionLog(), PHP_EOL;
            }
        }
        self::writeLog();
        if ($isError) {
            if (self::$shouldExit === false) {
                self::$exception = null;
                self::$isError = null;
                self::disableErrorReporting();
                return;
            }
        }
//        $headers = null;
//        $outputBuffer = null;
//        if (headers_sent()) {
//            if (self::$isDebugEnabled) {
//                $headers = headers_list();
//            } else {
//                exit(1);
//            }
//        } else {
//            if (self::$isDebugEnabled) {
//                $outputBuffer = static::getOutputBuffer();
//                $headers = headers_list();
//            } else {
//                static::cleanOutputBuffer();
//            }
//            header_remove();
//            if ($exception instanceof HttpException) {
//                $exception->setHeader();
//            } else {
//                header('HTTP/1.1 500 Internal Server Error');
//            }
//        }
//        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
//            if (self::$isDebugEnabled) {
//                static::renderDebugPage($headers, $outputBuffer);
//            } else {
//                static::renderCustomErrorPage();
//            }
//        }
        exit(1);
    }

    final public static function handleError(
        $type, $message, $file, $line, $context, $isFatal = false
    ) {
        $code = $isFatal ? 1 : 0;
        return self::handleException(
            new ErrorException($message, $code, $type, $file, $line), true
        );
    }

    final public static function handleFatalError() {
        $error = error_get_last();
        if ($error === null) {
            return;
        }
        if (ErrorCodeHelper::isFatalError($error['type'])) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line'],
                null,
                true
            );
        }
    }

    private static function disableErrorReporting() {
        $tmp = 0;
        if (self::$errorReporting & E_COMPILE_WARNING) {
            $tmp = E_COMPILE_WARNING;
        }
        error_reporting($tmp);
    }

    protected static function getDefaultErrorLog() {
        $exception = self::$exception;
        return ErrorCodeHelper::toString($exception->getSeverity())
            . ': ' . $exception->getMessage() . ' in ' . $exception->getFile()
            . ' on line ' . $exception->getLine();
    }

    protected static function getDefaultExceptionLog() {
        return 'Fatal error: Uncaught ' . self::$exception;
    }

    protected static function writeLog() {
        $isError = self::$isError;
        $exception = self::$exception;
        if (Config::get('hyperframework.error_handler.enable_logger')) {
            $name = null;
            $data = array();
            $method = null;
            if ($isError) {
                $name = 'hyperframework.error_handler.error';
                $data['severity'] = ErrorCodeHelper::toString(
                    $exception->getSeverity()
                );
            } else {
                $name = 'hyperframework.error_handler.exception';
                $data['class'] = get_class($exception);
                $code = $exception->getCode();
                if ($code !== null) {
                    $data['code'] = $code;
                }
            }
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
            if ($isError === false || $exception->getCode() === 0) {
                $data['traces'] = array();
                $traces = $exception->getTrace();
                if ($isError) {
                    array_shift($traces);
                }
                foreach ($traces as $item) {
                    $trace = array();
                    if (isset($item['class'])) {
                        $trace['class'] = $item['class'];
                    }
                    if (isset($item['function'])) {
                        $trace['function'] = $item['function'];
                    }
                    if (isset($item['file'])) {
                        $trace['file'] = $item['file'];
                    }
                    if (isset($item['line'])) {
                        $trace['line'] = $item['line'];
                    }
                    $data['traces'][] = $trace;
                }
            }
            $method = self::getLogMethod();
            Logger::$method($name, $exception->getMessage(), $data);
        } else {
            $message = null;
            if($isError) {
                $message = 'PHP ' . self::getDefaultErrorLog();
            } else {
                $message = 'PHP ' . self::getDefaultExceptionLog();
            }
            error_log($message);
        }
    }

    private static function getLogMethod() {
        if (self::$shouldExit) {
            return 'fatal';
        }
        return 'info';
//        $maps = array(
//            E_STRICT            => 'info',
//            E_DEPRECATED        => 'info',
//            E_USER_DEPRECATED   => 'info',
//            E_NOTICE            => 'notice',
//            E_USER_NOTICE       => 'notice',
//            E_WARNING           => 'warn',
//            E_USER_WARNING      => 'warn',
//        );
//        return $maps[self::$exception->getSeverity()];
    }

    final protected static function getException() {
        return self::$exception;
    }

    final protected static function isError() {
        return self::$isError;
    }

    final protected static function shouldExit() {
        return self::$shouldExit;
    }

    protected static function getIgnoredErrors() {
        return self::$ignoredErrors;
   }
}
