<?php
namespace Hyperframework\Web;

use ErrorException;
use Hyperframework\ErrorCodeHelper;
use Hyperframework\Web\Html\DebugPage;
use Hyperframework\Logger;
use Hyperframework\Config;

class ErrorHandler {
    private static $exception;
    private static $isDebugEnabled;
    private static $outputBufferLevel;
    private static $ignoredErrors;
    private static $exitLevel;
    private static $errorReporting;

    final public static function run() {
        self::$isDebugEnabled = ini_get('display_errors') === '1';
        self::$errorReporting = error_reporting();
        $class = get_called_class();
        if (self::$isDebugEnabled) {
            set_error_handler(array($class, 'handleError'), E_ALL | E_STRICT);
        } else {
            set_error_handler(
                array($class, 'handleError'), self::$error_reporting
            );
        }
        set_exception_handler(array($class, 'handleException'));
        register_shutdown_function(array($class, 'handleFatalError'));
        if (self::$isDebugEnabled) {
            ob_start();
            self::$outputBufferLevel = ob_get_level();
        }
        $tmp = 0;
        if (self::$errorReporting & E_COMPILE_WARNING) {
            $tmp = E_COMPILE_WARNING;
        }
        error_reporting($tmp);
    }

    protected static function getExitLevel() {
        if (self::$exitLevel === null) {
            $exitLevel = Config::get('hyperframework.error_handler.exit_level');
            if ($exitLevel == null) {
                $exitLevel === 'notice';
            }
        }
        //[notice | warning | error] use firelogger to see others (or use int code)
        //todo convert to bit
    }

    final public static function handleException($exception) {
        if (self::$exception !== null) {
            return false;
        }
        error_reporting(self::$errorReporting);
        self::$exception = $exception;
        if ($exception instanceof ErrorException) {
            self::writeErrorLog($exception);
        } else {
            self::writeExceptionLog($exception);
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
        if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            if (self::$isDebugEnabled) {
                static::renderDebugPage($headers, $outputBuffer);
            } else {
                static::renderCustomErrorPage();
            }
        }
        exit(1);
    }

    final public static function handleError(
        $type, $message, $file, $line, $context, $isFatal = false
    ) {
        $code = $isFatal ? 1 : 0;
        $e = new ErrorException($message, $code, $type, $file, $line);
        if (self::$isDebugEnabled === false) {
            return self::handleException($e);
        }
        if (error_reporting() & $type) {
            self::handleException($e);
        } else {
            if (self::$ignoredErrors === null) {
                self::$ignoredErrors = array();
            }
            self::$ignoredErrors[] = array(
                'type' => $type,
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'context' => $context
            );
        }
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
            self::$exception, self::$ignoredErrors, $headers, $outputBuffer
        );
    }

    protected static function renderCustomErrorPage() {
        ViewDispatcher::run(
            PathInfo::get('/', 'ErrorApp'), self::$exception
        );
    }

    protected static function writeErrorLog($exception) {
        $message = 'PHP ' . ErrorCodeHelper::toString($exception->getSeverity())
            . ': ' . $exception->getMessage() . ' in ' . $exception->getFile()
            . ':'. $exception->getLine() . PHP_EOL . 'Stack trace:'
            . $exception->getTraceAsString();
        self::writeLog($message, $exception->getSeverity());
    }

    protected static function writeExceptionLog() {
        self::writeLog(
            'PHP Fatal error: Uncaught ' . self::$exception, E_ERROR
        );
    }

    protected static function writeLog($message, $severity) {
        if (Config::get('hyperframework.logger.enable')) {
            $method = self::getLogMethod($severity);
            $logger = Config::get('hyperframework.logger.class');
            if ($logger) {
               $logger::$method($message);
            } else {
                Logger::$method($message);
            }
        } else {
            error_log($message);
        }
    }

    protected static function getLogMethod($severity) {
        $maps = array(
            E_STRICT            => 'info',
            E_DEPRECATED        => 'info',
            E_USER_DEPRECATED   => 'info',
            E_NOTICE            => 'notice',
            E_USER_NOTICE       => 'notice',
            E_WARNING           => 'warn',
            E_USER_WARNING      => 'warn',
            //E_COMPILE_WARNING   => 'warn',
            //E_CORE_WARNING      => 'warn',
            E_USER_ERROR        => 'error',
            E_RECOVERABLE_ERROR => 'error',
            E_ERROR             => 'fatal',
            E_COMPILE_ERROR     => 'fatal',
            E_PARSE             => 'fatal',
            //E_CORE_ERROR        => 'fatal'
        );
        return $maps[$severity];
    }

    protected static function getException() {
        return self::$exception;
    }

    protected static function getIgnoredErrors() {
        return self::$ignoredErrors;
    }

    public static function reset() {
        self::$exception = null;
        self::$isDebugEnabled = null;
        self::$outputBufferLevel = null;
        self::$ignoredErrors = null;
    }
}
