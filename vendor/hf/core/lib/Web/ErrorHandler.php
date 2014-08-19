<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\Web\Html\DebugPage;
use ErrorException;

class ErrorHandler {
    private static $exception;
    private static $isDebugEnabled;
    private static $outputBufferLevel;

    final public static function run() {
        $class = get_called_class();
        set_error_handler(array($class, 'handleError'), E_ALL);
        set_exception_handler(array($class, 'handleException'));
        register_shutdown_function(array($class, 'handleFatalError'));
        if (ini_get('display_errors') === '1') {
            self::$isDebugEnabled = true;
            ini_set('display_errors', false);
            ob_start();
            self::$outputBufferLevel = ob_get_level();
        }
    }

    final public static function handleFatalError() {
        if (self::$isDebugEnabled === false) {
            return;
        }
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
                $error['line'],
                true
            );
        }
    }

    final public static function handleError(
        $type, $message, $file, $line, $isFatal = false
    ) {
        if (error_reporting() & $type) {
            if (self::$isDebugEnabled) {
                ini_set('display_errors', true);
            }
            if ($isFatal === false) {
                self::writeErrorLog($type, $message, $file, $line, $isFatal);
            }
            $code = $isFatal ? 1 : 0;
            return self::handleException(new ErrorException(
                $message, $code, $type, $file, $line
            ));
        }
    }

    final public static function handleException($exception) {
        if (self::$exception !== null) {
            return false;
        }
        self::$exception = $exception;
        if ($exception instanceof ErrorException === false) {
            if (self::$isDebugEnabled) {
                ini_set('display_errors', true);
            }
            self::writeExceptionLog($exception);
        }
        if (self::$isDebugEnabled !== true) {
            static::cleanOutputBuffer();
        } else {
            $outputBuffer = static::getOutputBuffer();
            $headers = headers_list();
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

    protected static function writeErrorLog(
        $type, $message, $file, $line, $isFatal
    ) {
        $levels = array(
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
        self::writeLog(self::$exception);
    }

    protected static function writeExceptionLog($exception) {
        self::writeLog($exception);
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
