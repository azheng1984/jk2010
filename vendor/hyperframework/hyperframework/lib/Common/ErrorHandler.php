<?php
namespace Hyperframework\Common;

use Exception;
use Hyperframework\Common\ErrorException;
use Hyperframework\Common\Config;
use Hyperframework\Logging\Logger;

class ErrorHandler {
    private static $source;
    private static $isError;
    private static $isLoggerEnabled;
    private static $errorReportingBitmask;
    private static $isShutdownStarted = false;
    private static $shouldExit;
    private static $shouldDisplayErrors;
    private static $shouldReportCompileWarning;
    private static $isDefaultErrorLogEnabled;
    private static $isRunning = false;

    public static function run() {
        self::$isLoggerEnabled = Config::getBoolean(
            'hyperframework.error_handler.logger.enable'
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
            [$class, 'handleError'], self::$errorReportingBitmask
        );
        set_exception_handler([$class, 'handleException']);
        register_shutdown_function([$class, 'handleFatalError']);
        self::$isRunning = true;
        self::disableDefaultErrorReporting();
    }

    private static function enableDefaultErrorReporting(
        $errorReportingBitmask = null
    ) {
        //Error Control Operator - @
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
        if (self::$shouldReportCompileWarning === null) {
            self::$shouldReportCompileWarning =
            (self::getErrorReportingBitmask() & E_COMPILE_WARNING) !== 0;
        }
        return self::$shouldReportCompileWarning;
    }

    protected static function shouldDisplayErrors() {
        if (self::$isRunning === false) {
            throw new Exception;
        }
        return self::$shouldDisplayErrors;
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
        $shouldThrow = false;
        if (self::$source === null) {
            $errorThrowingBitmask = Config::getInt(
                'hyperframework.error_handler.error_throwing_bitmask', null
            );
            if ($errorThrowingBitmask === null) {
                $errorThrowingBitmask =
                    E_ALL & ~(E_STRICT | E_DEPRECATED | E_USER_DEPRECATED);
            }
            if (($type & $errorThrowingBitmask) !== 0) {
                $shouldThrow = true;
            }
        }
        $trace = array_slice(debug_backtrace(), 2);
        $error = new ErrorException(
            $message, $type, $file, $line, $trace, $context, $shouldThrow
        );
        return self::handle($error, true);
    }

    final public static function handleFatalError() {
        self::$isShutdownStarted = true;
        if (error_reporting() === 0) {
            return;
        }
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
        $error = new ErrorException(
            $error['message'], $error['type'], $error['file'],
            $error['line'], null, null
        );
        if ($error->isFatal()) {
            self::enableDefaultErrorReporting();
            self::handle($error, true);
        }
    }

    private static function handle($source, $isError = false) {
        if (self::$source !== null) {
            if ($isError && $source->isFatal()) {
                //fatal error from non-fatal error handler
                return;
            }
            if ($isError === false) {
                //exception from non-fatal error handler
                throw new $source;
            }
            return false;
        }
        if ($isError && $source->shouldThrow()) {
            throw $source;
        }
        if ($isError && $source->isFatal() === false) {
            self::$shouldExit = false;
        } else {
            self::$shouldExit = true;
        }
        self::$source = $source;
        self::$isError = $source instanceof ErrorException;
        static::writeLog();
        if (self::$shouldExit === false) {
            if (static::shouldDisplayErrors()) {
                static::displayError();
            }
            self::$source = null;
            self::disableDefaultErrorReporting();
            return;
        }
        static::displayFatalError();
        if (self::$isShutdownStarted) {
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
                $data['type'] = $source->getSeverityAsString();
            }
            if (self::isError() === false || $source->isFatal() === false) {
                $shouldLogTrace = Config::getBoolean(
                    'hyperframework.error_handler.logger.log_stack_trace'
                );
                if ($shouldLogTrace) {
                   $data['trace'] = [];
                    foreach ($source->getTrace() as $item) {
                        $trace = [];
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
        }
        if (static::isDefaultErrorLogEnabled()) {
            static::writeDefaultErrorLog();
        }
    }

    protected  static function writeDefaultErrorLog() {
        if (self::$isError) {
            error_log('PHP ' . self::getErrorLog());
        } else {
            error_log('PHP ' . self::getExceptionErrorLog());
        }
    }

    private static function getExceptionErrorLog() {
        return 'Fatal error:  Uncaught ' . self::$source . PHP_EOL
            . '  thrown in ' . self::$source->getFile() . ' on line '
            . self::$source->getLine();
    }

    private static function getErrorLog() {
        $error = self::$source;
        if ($error->shouldThrow() === true && $error->isFatal() === false) {
            $result = 'Fatal error';
        } else {
            $result = self::convertErrorTypeForOutput($error->getSeverity());
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
        return $maps[self::$source->getSeverity()];
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
            if ($source->shouldThrow() === true
                && $source->isFatal() === false
            ) {
                echo 'Fatal error';
            } else {
                echo self::convertErrorTypeForOutput($source->getSeverity());
            }
            echo '</b>:  ';
            if (ini_get('docref_root') !== '') {
                echo $source->getMessage();
            } else {
                echo htmlspecialchars(
                    $source->getMessage(),
                    ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                );
            }
        } else {
            echo 'Fatal error</b>:  Uncaught ', htmlspecialchars(
                self::$source,
                ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            ), PHP_EOL, '  thrown';
        }
        echo ' in <b>', htmlspecialchars(
                self::$source->getFile(),
                ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            ), '</b> on line <b>', self::$source->getLine(),
            '</b><br />', PHP_EOL, $suffix;
    }
}
