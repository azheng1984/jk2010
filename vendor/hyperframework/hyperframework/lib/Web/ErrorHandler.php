<?php
namespace Hyperframework\Web;

use Exception;
//use ErrorException;
use Hyperframework\Common\Error;
use Hyperframework\Common\Config;
use Hyperframework\Common\ErrorCodeHelper;
use Hyperframework\Logging\Logger;
use Hyperframework\Web\Html\Debugger;

class ErrorHandler {
    private static $source;
    private static $isError;
    private static $errorReporting;
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

    final public static function handleException($exception) {
        error_reporting(self::$errorReporting);
        self::handle($exception);
    }

    final public static function handleError(
        $type, $message, $file, $line, array $context
    ) {
        error_reporting(self::$errorReporting);
        $isFatal = false;
        $extraFatalErrorBitmask = Config::get(
            'hyperframework.error_handler.extra_fatal_error_bitmask'
        );
        if ($extraFatalErrorBitmask === null) {
            $extraFatalErrorBitmask =
                E_ALL & ~(E_STRICT | E_DEPRECATED | E_USER_DEPRECATED);
        }
        if (($type & $extraFatalErrorBitmask) !== 0) {
            $isFatal = true;
        }
        $trace = array_slice(debug_backtrace(), 2);
        return self::handle(
            new Error(
                $type, $message, $file, $line, $context, $trace, $isFatal
            ),
            true
        );
    }

    final public static function handleFatalError() {
        error_reporting(self::$errorReporting);
        $error = error_get_last();
        if ($error === null) {
            return;
        }
        $error = new Error(
            $error['type'], $error['message'], $error['file'],
            $error['line'], null, null, true
        );
        if ($error->isRealFatal()) {
            self::handle($error, true);
        }
    }

    private static function handle($source, $isError = false) {
        if (self::$source !== null) {
            if ($isError) {//fatal error
                return;
            }
            throw $source;
        }
        self::$source = $source;
        self::$isError = $isError;
        if ($isError && $source->isFatal() === false) {
            self::$shouldExit = false;
        } else {
            self::$shouldExit = true;
        }
        self::writeLog();
        if ($isError) {
            if (self::$shouldExit === false) {
                if (self::$shouldDisplayErrors) {
                    static::displayError();
                }
                self::$source = null;
                self::$isError = null;
                self::$previousErrors[] = $source;
                self::disableErrorReporting();
                return;
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
        if (self::$isDebuggerEnabled) {
            $headers = headers_list();
            if (headers_sent() === false) {
                header_remove();
            }
            $outputBuffer = static::getOutputBuffer();
            static::executeDebugger($headers, $outputBuffer);
        } elseif (self::$shouldDisplayErrors) {
            static::displayError();
        } elseif (headers_sent() === false) {
            header_remove();
            self::cleanOutputBuffer();
            static::renderCustomErrorView();
        }
        if ($isError && $source->isRealFatal()) {
            return;
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
            ['source' => self::$exception, 'type' => $type]
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

    protected static function writeLog() {
        if (self::$isLoggerEnabled) {
            $source = self::$source;
            $name = null;
            $data = [];
            $data['file'] = $source->getFile();
            $data['line'] = $source->getLine();
            if (self::$isError === false) {
                $name = 'php_exception';
                $data['class'] = get_class($source);
                $code = $source->getCode();
                if ($code !== null) {
                    $data['code'] = $code;
                }
                $data['trace'] = [];
                //config, error too
                foreach ($source->getTrace() as $item) {
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
                    $data['trace'][] = $trace;
                }
            } else {
                $name = 'php_error';
                $data['type'] = strtolower($source->getTypeAsString());
            }
            $method = self::getLogMethod();
            Logger::$method([
                'name' => $name,
                'message' => $source->getMessage(),
                'data' => $data
            ]);
        } else {
            if (ini_get('log_errors') === '1') {
                static::writeDefaultErrorLog();
            }
        }
    }

    protected static function writeDefaultErrorLog() {
        if (self::$isError) {
            error_log('PHP ' . self::$source);
        } else {
            error_log('PHP Fatal error:  Uncaught '
                . self::$source. PHP_EOL . '  thrown in '
                . self::$source->getFile() . ' on line '
                . self::$source->getLine()
            );
        }
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
        return $maps[self::$source->getType()];
    }

    final protected static function getSource() {
        return self::$source;
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

    final protected static function isLoggerEnabled() {
        return self::$isLoggerEnabled;
    }

    protected static function getPreviousErrors() {
        return self::$previousErrors;
    }

    private static function disableErrorReporting() {
        if (self::$errorReporting & E_COMPILE_WARNING) {
            error_reporting(E_COMPILE_WARNING);
            return;
        }
        error_reporting(0);
    }

    protected static function displayError() {
        if (ini_get('xmlrpc_errors') === '1') {
            $code = ini_get('xmlrpc_error_number');
            echo '<?xml version="1.0"?><code></code>';
            return;
        }
        $isHtml = ini_get('html_errors') === '1';
        $prependString = ini_get('error_prepend_string');
        $appendString = ini_get('error_append_string');
        if ($isHtml === false) {
            echo $prependString . PHP_EOL  . 'PHP ';
            if ($isError === false) {
                echo 'Fatal error: Uncaught ';
            }
            echo self::$source;
            if ($isError === false) {
                echo '  thrown in ',
                    self::$source->getFile(), ' on line ',
                    self::$source->getLine();
            }
            echo $appendString;
            return;
        }
        $source = self::$source;
        if (self::$isError) {
            echo  $prependString . '<br/><b>'
                . $source->getTypeAsString() . '</b>'
                . ': ' . $source->getMessage()
                . ' in <b>' . $source->getFile()
                . '</b> on line <b>' . $source->getLine() . '</b><br/>'
                . $appendString;
        } else {
            echo '<b>Fatal error</b>: Uncaught ';
            echo self::$source;
            echo '  thrown in <b>',
                self::$source->getFile(), '</b> on line <b>',
                self::$source->getLine() . '</b><br/>';
            echo $appendString;
        }
    }
}
