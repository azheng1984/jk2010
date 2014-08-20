<?php
namespace Hyperframework\Web;

use ErrorException;
use Hyperframework\ErrorCodeHelper;
use Hyperframework\Web\Html\DebugPage;

class ErrorHandler {
    private static $exception;
    private static $isDebugEnabled;
    private static $outputBufferLevel;

    final public static function run() {
        $class = get_called_class();
        set_error_handler(array($class, 'handleError'), E_ALL);
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
        } else {
            if (headers_sent()) {
            }
        }
        if ($exception instanceof ErrorException) {
            self::writeErrorLog($exception);
        } else {
            self::writeExceptionLog($exception);
        }
        if (self::$isDebugEnabled) {
            $outputBuffer = static::getOutputBuffer();
            $headers = headers_list();
        } else {
            static::cleanOutputBuffer();
        }
        header_remove();
        if ($exception instanceof HttpException === false) {
            $exception = new InternalServerErrorException;
        }
        $exception->setHeader();
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
        $type, $message, $file, $line, $isFatal = false
    ) {
        if (error_reporting() & $type) {
            $code = $isFatal ? 1 : 0;
            return self::handleException(new ErrorException(
                $message, $code, $type, $file, $line
            ));
        }
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
        $headers = headers_list();
        foreach ($headers as $header) {
            $header = str_replace(' ', '', strtolower($header));
            if ($header === 'content-encoding:gzip') {
                $result = file_get_contents(
                    'compress.zlib://data:;base64,' . base64_encode($content)
                );
                if ($result !== false) {
                    return $result;
                }
                break;
            } elseif ($header === 'content-encoding:deflate') {
                $result = gzinflate($content);
                if ($result !== false) {
                    return $result;
                }
                break;
            }
        }
        return $content;
    }

    protected static function renderDebugPage($headers, $outputBuffer) {
        DebugPage::render(self::$exception, $headers, $outputBuffer);
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

    public static function reset() {
        self::$exception = null;
    }
}
