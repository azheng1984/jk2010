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
    private static $shouldCacheErrors = false;
    private static $previousErrors = [];
    private static $errorReportingBitmask;
    private static $isShutdownStarted = false;
    private static $shouldExit;
    private static $shouldDisplayErrors;
    private static $isDefaultErrorLogEnabled;
    private static $isRunning = false;

    public static function run() {
        self::$isLoggerEnabled = Config::getBoolean(
            'hyperframework.error_handler.enable_logger', false
        );
        if (self::$isLoggerEnabled) {
            ini_set('log_errors', '0');
            self::$isDefaultErrorLogEnabled = false;
        } else {
            self::$isDefaultErrorLogEnabled = ini_get('log_errors') === '1';
        }
        self::$errorReportingBitmask = error_reporting();
        self::$shouldDisplayErrors = ini_get('display_errors') === '1';
        $class = get_called_class();
        set_error_handler(
            array($class, 'handleError'), self::$errorReportingBitmask
        );
        set_exception_handler(array($class, 'handleException'));
        register_shutdown_function(array($class, 'handleFatalError'));
        self::$isRunning = true;
        self::disableDefaultErrorReporting();
    }

    private static function enableDefaultErrorReporting(
        $errorReportingBitmask = null
    ) {
        if ($errorReportingBitmask !== null) {
            error_reporting($errorReportingBitmask);
        } elseif (self::shouldReportCompileWarning()) {
            error_reporting(static::getErrorReportingBitmask());
        }
        if (self::shouldReportCompileWarning() === false) {
            if (static::shouldDisplayErrors()) {
                ini_set('display_errors', '1');
            }
            if (static::isDefaultErrorLogEnabled()) {
                ini_set('log_errors', '1');
            }
        }
    }

    private static function disableDefaultErrorReporting() {
        if (self::shouldReportCompileWarning()) {
            error_reporting(E_COMPILE_WARNING);
        } else {
            if (static::shouldDisplayErrors()) {
                ini_set('display_errors', '0');
            }
            if (static::isDefaultErrorLogEnabled()) {
                ini_set('log_errors', '0');
            }
        }
    }

    private static function shouldReportCompileWarning() {
        return (self::getErrorReportingBitmask() & E_COMPILE_WARNING) !== 0;
    }

    protected static function shouldDisplayErrors() {
        if (self::$isRunning === false) {
            throw new Exception;
        }
        return self::$shouldDisplayErrors;
    }

    final protected static function enableErrorCache() {
        self::$shouldCacheErrors = true;
    }

    final protected static function disableErrorCache() {
        self::$shouldCacheErrors = false;
    }

    final public static function handleException($exception) {
        if (error_reporting() === 0) {
            return;
        }
        self::enableDefaultErrorReporting();
        self::handle($exception);
    }

    final public static function handleError(
        $type, $message, $file, $line, array $context
    ) {
        if (error_reporting() === 0) {
            return;
        }
        self::enableDefaultErrorReporting();
        $isFatal = false;
        $extraFatalErrorBitmask = Config::getInt(
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
        if (error_reporting() === 0) {
            return;
        }
        self::$isShutdownStarted = true;
        self::enableDefaultErrorReporting(
            error_reporting() | (static::getErrorReportingBitmask() & (
                E_ERROR | E_PARSE | E_CORE_ERROR
                    | E_COMPILE_ERROR | E_COMPILE_WARNING
            ))
        );
        $error = error_get_last();
        if ($error === null) {
            return;
        }
        $error = new Error(
            $error['type'], $error['message'], $error['file'],
            $error['line'], null, null, true
        );
        if ($error->isRealFatal()) {
            self::enableDefaultErrorReporting();
            self::handle($error, true);
        }
    }

    private static function handle($source, $isError = false) {
        if (self::$source !== null) {
            if ($isError) {//real fatal error or from excaption handler
                return false;
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
        static::writeLog();
        if ($isError) {
            if (self::$shouldExit === false) {
                if (static::shouldDisplayErrors()) {
                    static::displayError();
                }
                if (self::$shouldCacheErrors) {
                    self::$previousErrors[] = $source;
                }
                self::$source = null;
                self::disableDefaultErrorReporting();
                return;
            }
        }
        static::displayFatalError();
        if (($isError && $source->isRealFatal()) || self::$isShutdownStarted) {
            return;
        }
        exit(1);
    }

    protected static function displayFatalError() {
        static::displayError();
    }

    protected static function writeLog() {
        if (static::isLoggerEnabled()) {
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
            } else {
                $name = 'php_error';
                $data['type'] = $source->getTypeAsString();
            }
            if (self::isError() === false || $source->isRealFatal() === false) {
                $shouldLogTrace = Config::getBoolean(
                    'hyperframework.error_handler.log_stack_trace', false
                );
                if ($shouldLogTrace) {
                   $data['trace'] = [];
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
                }
            }
            $method = static::getLoggerMethod();
            Logger::$method([
                'name' => $name,
                'message' => $source->getMessage(),
                'data' => $data
            ]);
        } elseif (static::isDefaultErrorLogEnabled()) {
            static::writeDefaultErrorLog();
        }
    }

    protected static function writeDefaultErrorLog() {
        if (self::$isError) {
            error_log('PHP ' . self::getErrorLog());
        } else {
            error_log('PHP ' . self::getExceptionErrorLog());
        }
    }

    private static function getExceptionErrorLog() {
        return 'Fatal error:  Uncaught ' . self::$source. PHP_EOL
            . '  thrown in ' . self::$source->getFile() . ' on line '
            . self::$source->getLine();
    }

    private static function getErrorLog() {
        $error = self::$source;
        if ($error->isFatal() === true && $error->isRealFatal() === false) {
            $result = 'Fatal error';
        } else {
            $result = self::convertErrorTypeForOutput($error->getType());
        }
        return $result . ':  ' . $error->getMessage() . ' in '
            . $error->getFile() . ' on line ' . $error->getLine();
    }

    private static function convertErrorTypeForOutput($type) {
        switch ($type) {
            case E_STRICT:            return 'Strict standards';
            case E_DEPRECATED:
            case E_USER_DEPRECATED:   return 'Deprecated';
            case E_NOTICE:
            case E_USER_NOTICE:       return 'Notice';
            case E_WARNING:
            case E_USER_WARNING:      return 'Warning';
            case E_COMPILE_WARNING:   return 'Compile warning';
            case E_CORE_WARNING:      return 'Core warning';
            case E_USER_ERROR:        return 'Error';
            case E_RECOVERABLE_ERROR: return 'Recoverable error';
            case E_COMPILE_ERROR:     return 'Compile error';
            case E_PARSE:             return 'Parse error';
            case E_ERROR:             return 'Fatal error';
            case E_CORE_ERROR:        return 'Core error';
        }
    }

    protected static function getLoggerMethod() {
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

    final protected static function getPreviousErrors() {
        if (self::$shouldCacheErrors) {
            return self::$previousErrors;
        }
        return;
    }

    protected static function isLoggerEnabled() {
        if (self::$isRunning === false) {
            throw new Exception;
        }
        return self::$isLoggerEnabled;
    }

    protected static function isDefaultErrorLogEnabled() {
        if (self::$isRunning === false) {
            throw new Exception;
        }
        return self::$isDefaultErrorLogEnabled;
    }

    protected static function getErrorReportingBitmask() {
        if (self::$isRunning === false) {
            throw new Exception;
        }
        return self::$errorReportingBitmask;
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
                $message = self::getErrorLog();
            } else {
                $message = self::getExceptionErrorLog();
            }
            echo htmlspecialchars($message, ENT_XML1),
                '</string></value></member></struct></value></fault>',
                '</methodResponse>';
            return;
        }
        $isHtml = ini_get('html_errors') === '1';
        $prefix = ini_get('error_prepend_string');
        $suffix = ini_get('error_append_string');
        if ($isHtml === false) {
            echo $prefix, PHP_EOL;
            if (self::$isError === false) {
                echo self::getExceptionErrorLog();
            } else {
                echo self::getErrorLog();
            }
            echo PHP_EOL, $suffix;
            return;
        }
        echo $prefix, '<br />', PHP_EOL, '<b>';
        if (self::$isError) {
            if ($source->isFatal() === true
                && $source->isRealFatal() === false
            ) {
                echo 'Fatal error';
            } else {
                echo self::convertErrorTypeForOutput($source->getType());
            }
            echo '</b>:  ';
            if (ini_get('docref_root') !== '') {
                echo $source->getMessage();
            } else {
                echo htmlspecialchars($source->getMessage());
            }
        } else {
            echo 'Fatal error</b>:  Uncaught ', htmlspecialchars(self::$source),
                PHP_EOL, '  thrown';
        }
        echo ' in <b>', htmlspecialchars(self::$source->getFile()),
            '</b> on line <b>', self::$source->getLine(),
            '</b><br />', PHP_EOL, $suffix;
    }
}
