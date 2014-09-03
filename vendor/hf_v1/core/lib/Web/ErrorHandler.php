<?php
namespace Hyperframework\Web;

use ErrorException;
use Hyperframework\ErrorCodeHelper;
use Hyperframework\Web\Html\DebugPage;

class ErrorHandler {
    private static $exception;
    private static $isDebugEnabled;
    private static $outputBufferLevel;
    private static $ignoredErrors;

    final public static function run() {
        $class = get_called_class();
//        set_error_handler(array($class, 'handleError'), E_ALL);
        set_exception_handler(array($class, 'handleException'));
        register_shutdown_function(array($class, 'handleFatalError'));
        self::$isDebugEnabled = ini_get('display_errors') === '1';
        if (self::$isDebugEnabled) {
            ini_set('display_errors', false);
            ob_start();
            self::$outputBufferLevel = ob_get_level();
        }
    }

    final public static function handleException($exception) {
        if (self::$exception !== null) {
            return false;
        }
        self::$exception = $exception;
        if (self::$isDebugEnabled) {
            ini_set('display_errors', true);
        }
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
        if (error_reporting() & $type) {
            $code = $isFatal ? 1 : 0;
            return self::handleException(new ErrorException(
                $message, $code, $type, $file, $line
            ));
        }
        //todo write debug log on dev env
        if (self::$isDebugEnabled === false) {
            return;
        }
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

    final public static function handleFatalError() {
        if (self::$isDebugEnabled === false || self::$exception !== null) {
            return;
        }
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
        if ($exception->getCode() === 1) {
            return;
        }
        $message = 'PHP ' . ErrorCodeHelper::toString($exception->getSeverity())
            . ': ' . $exception->getMessage() . ' in ' . $exception->getFile()
            . ':'. $exception->getLine() . PHP_EOL . 'Stack trace:'
            . $exception->getTraceAsString();
        self::writeLog($message);
    }

    protected static function writeExceptionLog() {
        self::writeLog('PHP Fatal error: Uncaught ' . self::$exception);
    }

    protected static function writeLog($message) {
        error_log($message);
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
