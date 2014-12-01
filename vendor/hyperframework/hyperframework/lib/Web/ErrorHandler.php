<?php
namespace Hyperframework\Web;

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
    private static $isDebugEnabled;
    private static $outputBufferLevel;
    private static $errorReporting;
    private static $previousErrors = [];

    final public static function run() {
        self::$isDebugEnabled = ini_get('display_errors') === '1';
        self::$errorReporting = error_reporting();
        $class = get_called_class();
        set_error_handler(array($class, 'handleError'), self::$errorReporting);
        set_exception_handler(array($class, 'handleException'));
        register_shutdown_function(array($class, 'handleFatalError'));
        if (self::$isDebugEnabled) {
            ob_start();
            self::$outputBufferLevel = ob_get_level();
        }
        self::disableErrorReporting();
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
                self::disableErrorReporting();
                self::$previousErrors[] = $exception;
                return;
            }
        }
        $headers = null;
        $outputBuffer = null;
        if (headers_sent()) {
            if (self::$isDebugEnabled) {
                $headers = headers_list();
            } else {
                exit(1);
            }
        } else {
            if (self::$isDebugEnabled) {
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
        if (self::$isDebugEnabled) {
            static::renderDebugPage($headers, $outputBuffer);
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

    private static function disableErrorReporting() {
        if (self::$errorReporting & E_COMPILE_WARNING) {
            error_reporting(E_COMPILE_WARNING);
            return;
        }
        error_reporting(0);
    }

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

    protected static function renderDebugPage($headers, $outputBuffer) {
        DebugPage::render(
            self::$exception, self::$previousErrors, $headers, $outputBuffer
        );
    }

    protected static function renderCustomErrorPage() {
        //todo
        ViewDispatcher::run(
            PathInfo::get('/', 'ErrorApp'), self::$exception
        );
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
        $exception = self::$exception;
        if (Config::get('hyperframework.error_handler.enable_logger')) {
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
        } else {
           $message = null;
           if(self::$isError) {
               $message = self::getDefaultErrorLog();
           } else {
               $message = self::getDefaultExceptionLog();
           }
           error_log('PHP ' . $message);
        }
    }

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
