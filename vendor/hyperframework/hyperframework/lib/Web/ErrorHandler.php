<?php
namespace Hyperframework\Web;

use Exception;
use ErrorException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ErrorCodeHelper;
use Hyperframework\Logging\Logger;
use Hyperframework\Web\Html\Debugger;

class ErrorHandler {
    private static $exception;
    private static $isError;
    private static $shouldExit;
    private static $isDebuggerEnabled;
//    private static $isGlobalLoggerEnabled;
//    private static $shouldDisplayErrors = false;
    private static $outputBufferLevel;
//    private static $errorReporting;
    private static $previousErrors = [];

    final public static function run() {
        if (Config::get('hyperframework.error_handler.write_global_log')
            === false
        ) {
            ini_set('log_errors', '0');
        } else {
            if (Config::get('hyperframework.error_handler.enable_logger')
                    === true
                && Config::get('hyperframework.error_handler.write_global_log')
                    !== true
            ) {
                ini_set('log_errors' , '0');
            }
        }
        self::$isDebuggerEnabled =
            Config::get('hyperframework.web.debug') === true;
        //self::$errorReporting = error_reporting();
        $class = get_called_class();
        //set_error_handler(array($class, 'handleError'), self::$errorReporting);
        set_error_handler(array($class, 'handleError'));
        set_exception_handler(array($class, 'handleException'));
        register_shutdown_function(array($class, 'handleFatalError'));
        if (self::$isDebuggerEnabled) {
            ob_start();
            self::$outputBufferLevel = ob_get_level();
        }
//        self::disableErrorReporting();
    }

    final public static function handleException($exception, $isError = false) {
        if (self::$exception !== null) {
            if ($isError) {//fatal error only
                return false;
            }
            throw $exception;
        }
        //if (self::$isGlobalLoggerEnabled) {
        //    ini_set('log_errors', '1');
        //}
//      error_reporting(self::$errorReporting);
        self::$exception = $exception;
        self::$isError = $isError;
        //if (ini_get('display_errors') === '1') {
        //    if ($isError) {
        //        echo self::getDefaultErrorLog();
        //    } else {
        //        echo self::getDefaultExceptionLog();
        //    }
        //}
        //if ($shouldDisplayErrors) {
        //    if ($isError) {
        //        echo self::getDefaultErrorLog();
        //    } else {
        //        echo self::getDefaultExceptionLog();
        //    }
        //}
        self::writeLog();
        if ($isError && $exception->getCode() === 0) {
            $extraFatalErrorBitmask = Config::get(
                'hyperframework.error_handler.extra_fatal_error_bitmask'
            );
            if ($extraFatalErrorBitmask === null) {
                $extraFatalErrorBitmask =
                    E_ALL & ~(E_STRICT & E_DEPRECATED & E_USER_DEPRECATED);
            }
            self::$shouldExit = 0 !==
                (self::$exception->getSeverity() & $extraFatalErrorBitmask);
        } else {
            self::$shouldExit = true;
        }
        if ($isError) {
            if (self::$shouldExit === false) {
                self::$exception = null;
                self::$isError = null;
                self::disableGlobalLogger();
                self::$previousErrors[] = $exception;
                return;
            }
        }
        $headers = null;
        $outputBuffer = null;
        if (headers_sent()) {
            if (self::$isDebuggerEnabled) {
                $headers = headers_list();
            } else {
                exit(1);
            }
        } else {
            if (self::$isDebuggerEnabled) {
                $outputBuffer = static::getOutputBuffer();
                $headers = headers_list();
            } else {
                static::cleanOutputBuffer();
            }
            header_remove();
            if ($exception instanceof HttpException) {
                $exception->setHeader();
            } else {
                header('HTTP/1.1 500 Internal Server Error');
            }
        }
        if (self::$isDebuggerEnabled) {
            static::executeDebugger($headers, $outputBuffer);
        } else {
            static::renderCustomErrorPage();
        }
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

//    private static function disableErrorReporting() {
//        if (self::$isGlobalLoggerEnabled
//            && Config::get('hyperframework.error_handler.write_global_log')
//                !== true
//            && Config::get('hyperframework.error_handler.enable_logger')
//                === true
//        ) {
//            ini_set('log_errors', '0');
//        }
        //display true log false  //disable log directly, except compile warning
        //display false log true  //never disable
        //display true log true   //never disable
        //display false log false //never open
        //if (self::$errorReporting & E_COMPILE_WARNING) {
        //    error_reporting(E_COMPILE_WARNING);
        //    return;
        //}
        //error_reporting(0);
//    }

    protected static function cleanOutputBuffer() {
        $obLevel = ob_get_level();
        while ($obLevel > 0) {
            ob_end_clean();
            --$obLevel;
        }
    }

    protected static function getOutputBuffer() {
        $outputBufferLevel = ob_get_level();
        while ($outputBufferLevel > self::$outputBufferLevel) {
            ob_end_flush();
            --$outputBufferLevel;
        }
        $content = ob_get_contents();
        ob_end_clean();
        if ($content === '') {
            return;
        }
        $charset = null;
        $encoding = null;
        foreach (headers_list() as $header) {
            $header = str_replace(' ', '', strtolower($header));
            if ($header === 'content-encoding:gzip') {
                $encoding = 'gzip';
            } elseif ($header === 'content-encoding:deflate') {
                $encoding = 'deflate';
            } elseif (strncmp('content-type:', $header, 13) === 0) {
                $header = substr($header, 13);
                $segments = explode(';', $header);
                foreach ($segments as $segment) {
                    if (strncmp('charset=', $segment, 8) === 0) {
                        $charset = substr($segment, 8);
                        break;
                    }
                }
            }
        }
        if ($encoding !== null) {
            $content = static::decodeOutputBuffer($content, $encoding);
        } 
        if ($charset !== null) {
            $content = static::convertOutputBufferCharset($content, $charset);
        }
        return $content;
    }

    protected static function decodeOutputBuffer($content, $encoding) {
        if ($encoding === 'gzip') {
            $result = file_get_contents(
                'compress.zlib://data:;base64,' . base64_encode($content)
            );
            if ($result !== false) {
                $content = $result;
            }
        } elseif ($encoding === 'deflate') {
            $result = gzinflate($content);
            if ($result !== false) {
                $content = $result;
            }
        }
        return $content;
    }

    protected static function convertOutputBufferCharset($content, $charset) {
        if ($charset !== 'utf-8') {
            $result = iconv($charset, 'utf-8', $content);
            if ($result !== false) {
                $content = $result;
            }
        }
        return $content;
    }

    protected static function executeDebugger($headers, $outputBuffer) {
        Debugger::execute(
            self::$exception, self::$previousErrors, $headers, $outputBuffer
        );
    }

    protected static function renderCustomErrorPage() {
        $template = new ViewTemplate(['exception' => self::$exception]);
        $format = static::getCustomErrorPageFormat();
        $prefix = $template->getRootPath() . DIRECTORY_SEPARATOR
            . '_error' . DIRECTORY_SEPARATOR . 'show.';
        if ($format !== null && $format !== 'php') {
            if (file_exists($prefix . $format . '.php')) {
                $template->load('_error/show.' . $format . '.php');
                return;
            } 
        }
        if (file_exists($prefix . 'php')) {
            $template->load('_error/show.php');
            return;
        }
        if (self::$exception instanceof HttpException) {
            echo self::$exception->getCode();
        } else {
            echo '500 Internal Server Error';
        }
    }

    protected static function getCustomErrorPageFormat() {
        $pattern = '#\.([0-9a-zA-Z]+)$#';
        $requestPath = RequestPath::get();
        if (preg_match($pattern, $requestPath, $matches) === 1) {
            return $matches[1];
        }
    }

//    protected static function getDefaultErrorLog() {
//        $exception = self::$exception;
//        return ErrorCodeHelper::toString($exception->getSeverity())
//            . ': ' . $exception->getMessage() . ' in ' . $exception->getFile()
//            . ' on line ' . $exception->getLine();
//    }
//
//    protected static function getDefaultExceptionLog() {
//        return 'Fatal error: Uncaught ' . self::$exception;
//    }

    protected static function writeLog() {
        if (Config::get('hyperframework.error_handler.enable_logger')) {
            $exception = self::$exception;
            $name = null;
            $data = [];
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
            if (self::$isError === false) {
                $name = 'php_exception';
                $data['class'] = get_class($exception);
                $code = $exception->getCode();
                if ($code !== null) {
                    $data['code'] = $code;
                }
                $data['stack_trace'] = [];
                foreach ($exception->getTrace() as $item) {
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
                    $data['stack_trace'][] = $trace;
                }
            } else {
                $name = 'php_error';
                $data['severity'] = strtolower(ErrorCodeHelper::toString(
                    $exception->getSeverity()
                ));
            }
            $method = self::getLogMethod();
            Logger::$method($name, $exception->getMessage(), $data);
        }
    }

//    private static function writeGlobalLog() {
//        if (ini_get('log_errors') === '1') {
//            if(self::$isError) {
//                $message = self::getDefaultErrorLog();
//            } else {
//                $message = self::getDefaultExceptionLog();
//            }
//            error_log('PHP ' . $message);
//        }
//    }

    private static function getLogMethod() {
        if (self::$shouldExit) {
            return 'fatal';
        }
        $maps = array(
            E_STRICT            => 'info',
            E_DEPRECATED        => 'info',
            E_USER_DEPRECATED   => 'info',
            E_NOTICE            => 'notice',
            E_USER_NOTICE       => 'notice',
            E_WARNING           => 'warn',
            E_USER_WARNING      => 'warn',
            E_CORE_WARNING      => 'warn',
            E_RECOVERABLE_ERROR => 'error'
        );
        return $maps[self::$exception->getSeverity()];
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

    protected static function getPreviousErrors() {
        return self::$previousErrors;
    }
}
