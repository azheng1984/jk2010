<?php
namespace Hyperframework\Common;

use Exception;
use Hyperframework\Common\Error;
use Hyperframework\Common\Config;
use Hyperframework\Logging\Logger;

class ErrorHandler {
    private static $source;
    private static $isError;
    private static $isLoggerEnabled;
    private static $shouldRecordPreviousErrors = false;
    private static $previousErrors = [];
    private static $errorReporting;
    private static $shouldExit;

    public static function run() {
        self::$isLoggerEnabled =false;
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

    final protected static function enablePreviousErrorRecording() {
        self::$shouldRecordPreviousErrors = true;
    }

    final protected static function disablePreviousErrorRecording() {
        self::$shouldRecordPreviousErrors = false;
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
        self::handle(
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
        $shouldDisplayErrors = ini_get('display_errors') === '1';
        if ($isError) {
            if (self::$shouldExit === false) {
                if ($shouldDisplayErrors) {
                    static::displayError();
                }
                if (self::$shouldRecordPreviousErrors) {
                    self::$previousErrors[] = $source;
                }
                self::$source = null;
                self::disableErrorReporting();
                return;
            }
        }
        static::displayFatalError();
        if ($isError && $source->isRealFatal()) {
            return;
        }
        exit(1);
    }

    protected static function displayFatalError() {
        static::displayError();
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
                //config max trace, error too
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
            error_log('PHP ' . self::getExceptionErrorLog());
        }
    }

    private static function getExceptionErrorLog() {
        return 'Fatal error:  Uncaught '
            . self::$source. PHP_EOL . '  thrown in '
            . self::$source->getFile() . ' on line '
            . self::$source->getLine();
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
        if (self::$source === null) {
            throw new Exception;
        }
        return self::$isError;
    }

    final protected static function isException() {
        if (self::$source === null) {
            throw new Exception;
        }
        return !self::$isError;
    }

    final protected static function isLoggerEnabled() {
        return self::$isLoggerEnabled;
    }

    final protected static function getPreviousErrors() {
        if (self::$shouldRecordPreviousErrors) {
            return self::$previousErrors;
        }
        return false;
    }

    private static function disableErrorReporting() {
        if (self::$errorReporting & E_COMPILE_WARNING) {
            error_reporting(E_COMPILE_WARNING);
            return;
        }
        error_reporting(0);
    }

    protected static function displayError() {
        $source = self::$source;
        if (ini_get('xmlrpc_errors') === '1') {
            $code = ini_get('xmlrpc_error_number');
            echo '<?xml version="1.0"?><methodResponse>',
                '<fault><value><struct><member><name>faultCode</name>',
                '<value><int>', $code, '</int></value></member><member>',
                '<name>faultString</name><value><string>';
            if (self::$isError) {
                $message = $source;
            } else {
                $message = self::getExceptionErrorLog();
            }
            echo htmlspecialchars($message, ENT_XML1),
                '</string></value></member></struct></value></fault>',
                '</methodResponse>';
            return;
        }
        $isHtml = ini_get('html_errors') === '1';
        $prependString = ini_get('error_prepend_string');
        $appendString = ini_get('error_append_string');
        if ($isHtml === false) {
            echo $prependString, PHP_EOL;
            if (self::$isError === false) {
                echo self::getExceptionErrorLog();
            } else {
               echo $source;
            }
            echo PHP_EOL, $appendString;
            return;
        }
        echo $prependString, '<br />', PHP_EOL, '<b>';
        if (self::$isError) {
            echo  $source->getTypeAsString();
            if ($source->isFatal() === true
                && $source->isRealFatal() === false
            ) {
                echo '(Fatal)';
            }
            echo '</b>', ':  ', htmlspecialchars($source->getMessage());
        } else {
            echo 'Fatal error</b>:  Uncaught ', htmlspecialchars(self::$source),
                PHP_EOL, '  thrown';
        }
        echo ' in <b>', htmlspecialchars(self::$source->getFile()),
            '</b> on line <b>', self::$source->getLine(),
            '</b><br />', PHP_EOL, $appendString;
    }
}
