<?php
namespace Hyperframework\Web;

use Exception;
//use ErrorException;
use Hyperframework\Common\ErrorException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ErrorCodeHelper;
use Hyperframework\Logging\Logger;
use Hyperframework\Web\Html\Debugger;

class ErrorHandler {
    private static $exception;
    private static $errorReporting;
    private static $isError;
    private static $shouldExit;
    private static $shouldDisplayErrors;
    private static $isDebuggerEnabled;
    private static $isLoggerEnabled;
    private static $outputBufferLevel;
    private static $previousErrors = [];

    final public static function run() {
        self::$shouldDisplayErrors = ini_get('display_errors') === '1';
        self::$isDebuggerEnabled = false;
            Config::get('hyperframework.error_handler.debug');
        if (self::$shouldDisplayErrors) {
            if (self::$isDebuggerEnabled !== false) {
                self::$isDebuggerEnabled = true;
            }
        } else {
            if (self::$isDebuggerEnabled !== true) {
                self::$isDebuggerEnabled = false;
            }
        }
        if (self::$isDebuggerEnabled) {
            ob_start();
            self::$outputBufferLevel = ob_get_level();
        }
        self::$isLoggerEnabled = false;
            Config::get('hyperframework.error_handler.enable_logger') === true;
        if (self::$isLoggerEnabled) {
            ini_set('log_errors', '0');
        }
        self::$errorReporting = error_reporting();
        $class = get_called_class();
        set_error_handler(array($class, 'handleError'), self::$errorReporting);
        set_exception_handler(array($class, 'handleException'));
        register_shutdown_function(array($class, 'handleFatalError'));
        self::disableErrorReporting();
    }

    final public static function handleException($exception, $isError = false) {
        if (self::$exception !== null) {
            if ($isError) {//fatal error only
                return;
            }
            throw $exception;
        }
        //if (self::$isDebuggerEnabled && self::$shouldDisplayErrors) {
        //    ini_set('display_errors', '1');
        //}
        error_reporting(self::$errorReporting);
        self::$exception = $exception;
        self::$isError = $isError;
        if ($isError && $exception->isFatal() === false) {
            $extraFatalErrorBitmask = Config::get(
                'hyperframework.error_handler.extra_fatal_error_bitmask'
            );
            if ($extraFatalErrorBitmask === null) {
                $extraFatalErrorBitmask =
                    E_ALL & ~(E_STRICT | E_DEPRECATED | E_USER_DEPRECATED);
            }
            if (($exception->getSeverity() & $extraFatalErrorBitmask) !== 0) {
                self::$shouldExit = true;
            } else {
                self::$shouldExit = false;
            }
        } else {
            self::$shouldExit = true;
        }
        if (self::$isLoggerEnabled) {
            self::writeLog();
        }
        if ($isError) {
            if (self::$shouldExit === false) {
                if (self::$isDebuggerEnabled && self::$shouldDisplayErrors) {
                    self::displayError();
                    ini_set('display_errors', '0');
                }
                self::$exception = null;
                self::$isError = null;
                self::$previousErrors[] = $exception;
                return false;//trigger default logger
            }
        }
        if (headers_sent() === false) {
            if ($exception instanceof HttpException) {
                foreach ($exception->getHttpHeaders() as $header) {
                    header($header);
                }
            } else {
                header('HTTP/1.1 500 Internal Server Error');
            }
        }
        if (self::$isDebuggerEnabled || self::$shouldDisplayErrors === false) {
            if (ini_get('log_errors') === '1') {
                error_log(
                    'PHP Fatal error:  Uncaught '
                    . self::$exception . PHP_EOL . '  thrown in '
                    . self::$exception->getFile() . ' on line '
                    . self::$exception->getLine()
                );
            }
        }
        if (self::$isDebuggerEnabled) {
            $headers = headers_list();
            if (headers_sent() === false) {
                header_remove();
            }
            $outputBuffer = static::getOutputBuffer();
            static::executeDebugger($headers, $outputBuffer);
            if ($isError && $exception->isFatal()) {
                return;
            }
            exit(1);
        }
        if (self::$shouldDisplayErrors) {
            if ($isError && $exception->isFatal()) {
                return;
            }
            //if ($isError) {
            //    //display error
            //    //write error log
            //    //exit(1);
            //    return false;
            //}
            throw $exception;
        }
        if (headers_sent()) {
            exit(1);
        }
        header_remove();
        self::cleanOutputBuffer();
        static::renderCustomErrorView();
        exit(1);
    }

    final public static function handleError(
        $severity, $message, $file, $line, array $context = null
    ) {
        return self::handleException(
            new ErrorException(
                $message, 0, $severity, $file, $line, $context
            ),
            true
        );
    }

    final public static function handleFatalError() {
        $error = error_get_last();
        if ($error === null) {
            return;
        }
        if (ErrorCodeHelper::isFatal($error['type'])) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line'],
                null,
                null
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

    protected static function executeDebugger($headers, $outputBuffer) {
        Debugger::execute(
            self::$exception, self::$previousErrors, $headers, $outputBuffer
        );
    }

    protected static function renderCustomErrorView() {
        $type = null;
        if (self::$isError === true) {
            $type = 'error';
        } else {
            $type = 'exception';
        }
        $template = new ViewTemplate(
            ['source' => self::$exception, 'type' => self::$isError]
        );
        $format = static::getCustomErrorViewFormat();
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
        header('Content-Type:text/plain; charset=utf-8');
        if (self::$exception instanceof HttpException) {
            echo self::$exception->getCode();
        } else {
            echo '500 Internal Server Error';
        }
    }

    protected static function getCustomErrorViewFormat() {
        $pattern = '#\.([0-9a-zA-Z]+)$#';
        $requestPath = RequestPath::get();
        if (preg_match($pattern, $requestPath, $matches) === 1) {
            return $matches[1];
        }
    }

    private static function getDefaultErrorLog() {
        $exception = self::$exception;
        $message = PHP_EOL
            . ErrorCodeHelper::toString($exception->getSeverity());
        return  $message . ': ' . $exception->getMessage() . ' in '
            . $exception->getFile() . ' on line ' . $exception->getLine();
    }

    protected static function displayError() {
        //display xmlrpc  format error
        $isHtml = ini_get('html_errors') === '1';
        $prependString = ini_get('error_prepend_string');
        $appendString = ini_get('error_append_string');
        if ($isHtml === false) {
            echo $prependString . self::getDefaultErrorLog() . $appendString;
            return;
        }
        $exception = self::$exception;
        $message = $prependString . '<br/><b>'
            . ErrorCodeHelper::toString($exception->getSeverity()) . '</b>';
        echo $message . ': ' . $exception->getMessage()
            . ' in <b>' . $exception->getFile()
            . '</b> on line <b>' . $exception->getLine() . '</b><br/>'
            . $appendString;
    }

//    protected static function displayNonFatalErrorForDebugging() {
//        static::displayError();
//    }

    protected static function writeLog() {
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
            $data['type'] = strtolower(ErrorCodeHelper::toString(
                $exception->getSeverity()
            ));
        }
        $method = self::getLogMethod();
        Logger::$method([
            'name' => $name,
            'message' => $exception->getMessage(),
            'data' => $data
        ]);
    }

    private static function getLogMethod() {
        if (self::$shouldExit) {
            return 'fatal';
        }
        $maps = [
            E_STRICT            => 'info',
            E_DEPRECATED        => 'info',
            E_USER_DEPRECATED   => 'info',
            E_NOTICE            => 'notice',
            E_USER_NOTICE       => 'notice',
            E_WARNING           => 'warn',
            E_USER_WARNING      => 'warn',
            E_CORE_WARNING      => 'warn',
            E_RECOVERABLE_ERROR => 'error'
        ];
        return $maps[self::$exception->getSeverity()];
    }

    final protected static function getException() {
        return self::$exception;
    }

    final protected static function getSource() {
    }

    final protected static function isError() {
        return self::$isError;
    }

    final protected static function isException() {
        return !self::$isError;
    }

    final protected static function shouldExit() {
        return self::$shouldExit;
    }

    protected static function getPreviousErrors() {
        return self::$previousErrors;
    }

    private static function disableErrorReporting() {
        if (self::$errorReporting & E_COMPILE_WARNING) {
            error_reporting(E_COMPILE_WARNING);
        }
        error_reporting(0);
    }
}
