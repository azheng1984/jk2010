<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\Web\Html\ErrorPage;

class ErrorHandler {
    private static $exception;
    private static $log;
    private static $isOutputBufferEnabled;
    private static $isDebugEnabled;
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
        if (Config::get('hyperframework.web.error_handler.debug.enable') === true) {
            //只有在调试时关闭错误显示，否则按照服务器默认设置
            ini_set('display_errors', false);
            self::$isDebugEnabled = true;
            if (Config::get('hyperframework.web.error_handler.debug.enable_output_buffer') !== false) {
                if (ob_get_level() === 0 || headers_sent() === false) {
                    ob_start();
                    self::$isOutputBufferEnabled = true;
                }
            }
        }
        $class = get_called_class();
        set_error_handler(array($class, 'handleError'));
        set_exception_handler(array($class, 'handleException'));
        //register_shutdown_function(array($class, 'handleFatalError'));
    }

    final public static function handleError($type, $message, $file, $line) {
        if (error_reporting() & $type) {
            if (self::$isDebugEnabled) {
                //错误处理不可能被循环调用, 如果在错误处理时不开启 error 展示，应用内 error 无法显示
                ini_set('display_errors', true);
            }
            if (self::$exception !== null) {
                if (self::$isDebugEnabled) {
                    echo "\n" . self::$log;
                }
                return false;
            }
            self::$exception = new \ErrorException(
                $message, 0, $type, $file, $line
            );
            self::$log = self::$exception;
            self::handleException($exception);
        }
        return false;
    }

    final public static function handleException($exception) {
        echo 'hi2';
        return false;
        //echo 'hi';
        //echo xxxxxxx;
    }

    final public static function handleFatalError() {
        throw new adsf;
        //如果没有关闭错误展示，fatal error 在次之前就已经被输出了
        //echo xxx;
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
//    final public static function handleException($exception) {
//        if (self::$exception !== null || self::$error !== null) {
//            echo self::$log;
//            return false;
//        }
//        if (headers_sent() && self::$isDebugEnabled !== true) {
//            self::writeErrorLog($exception);
//            return;
//        }
//        self::$exception = $exception;
//        if ($exception instanceof HttpException === false) {
//            $exception = new InternalServerErrorException;
//        }
//        if (self::$isDebugEnabled === true) {
//            ErrorPage::renderException(self::$exception);
//            return;
//        }
//        static::resetOutput();
//        $exception->setHeader();
//        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
//            try {
//                static::renderCustomErrorPage(self::$exception);
//            } catch (\Exception $e) {
//                static::triggerError(self::$exception, $e);
//            }
//        }
//        if ($exception instanceof InternalServerErrorException) {
//            echo 'hi';
//            static::triggerError(self::$exception);
//        }
//    }
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
