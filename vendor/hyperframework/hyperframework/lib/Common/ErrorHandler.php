<?php
namespace Hyperframework\Common;

use Exception;
use Hyperframework\Logging\Logger;

class ErrorHandler {
    private $errorReportingBitmask;
    private $shouldReportCompileWarning;
    private $isDefaultErrorLogEnabled;
    private $isLoggerEnabled;
    private $shouldDisplayErrors;
    private $isShutdownStarted = false;
    private $shouldExit;
    private $error;

    public function __construct() {
        $this->isLoggerEnabled = Config::getBoolean(
            'hyperframework.error_handler.logger.enable', false
        );
        if ($this->isLoggerEnabled) {
            ini_set('log_errors', '0');
            $this->isDefaultErrorLogEnabled = false;
        } else {
            $this->isDefaultErrorLogEnabled = ini_get('log_errors') === '1';
        }
        $this->shouldDisplayErrors = ini_get('display_errors') === '1';
        $this->errorReportingBitmask = error_reporting();
    }

    public function run() {
        $this->registerErrorHandler();
        $this->registerExceptionHandler();
        $this->registerShutdownHandler();
        $this->disableDefaultErrorReporting();
    }

    protected function displayError() {
        $error = $this->error;
        if (ini_get('xmlrpc_errors') === '1') {
            $code = ini_get('xmlrpc_error_number');
            echo '<?xml version="1.0"?', '><methodResponse>',
                '<fault><value><struct><member><name>faultCode</name>',
                '<value><int>', $code, '</int></value></member><member>',
                '<name>faultString</name><value><string>';
            if ($error instanceof Exception) {
                $message = $this->getExceptionErrorLog();
            } else {
                $message = $this->getError();
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
            if ($error instanceof Exception) {
                echo $this->getExceptionErrorLog();
            } else {
                echo $this->getError();
            }
            echo PHP_EOL, $suffix;
            return;
        }
        echo $prefix, '<br />', PHP_EOL, '<b>';
        if ($error instanceof Exception === false) {
            echo $error->getCodeAsString(), '</b>:  ';
            if (ini_get('docref_root') !== '') {
                echo $error->getMessage();
            } else {
                echo htmlspecialchars(
                    $error->getMessage(),
                    ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                );
            }
        } else {
            echo 'Fatal error</b>:  Uncaught ', htmlspecialchars(
                $error,
                ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            ), PHP_EOL, '  thrown';
        }
        echo ' in <b>', htmlspecialchars(
                $error->getFile(), ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            ), '</b> on line <b>', $error->getLine(),
            '</b><br />', PHP_EOL, $suffix;
    }

    protected function displayFatalError() {
        $this->displayError();
    }

    private function getMaxLogLength() {
        $config = ini_get('log_errors_max_len');
        if (strlen($config) < 2) {
            return (int)$config;
        }
        $type = strtolower(substr($config, -1));
        $config = (int)$config;
        switch ($type) {
            case 'g':
                $config *= 1024;
            case 'm':
                $config *= 1024;
            case 'k':
                $config *= 1024;
        }
        return $config;
    }

    protected function writeLog() {
        if ($this->isLoggerEnabled()) {
            $method = $this->getLoggerMethod();
            $callback = function() {
                if ($this->error instanceof Exception) {
                    $message = 'PHP ' . $this->getExceptionErrorLog();
                } else {
                    $message = 'PHP ' . $this->getError();
                }
                $maxLength = $this->getMaxLogLength();
                if ($maxLength > 0) {
                    return substr($message, 0, $maxLength);
                }
                return $message;
            };
            $loggerClass = $this->getCustomLoggerClass();
            if ($loggerClass === null) {
                Logger::$method($callback);
            } else {
                $loggerClass::$method($callback);
            }
        } elseif ($this->isDefaultErrorLogEnabled()) {
            $this->writeDefaultErrorLog();
        }
    }

    private function getCustomLoggerClass() {
        $loggerClass = Config::getString(
            'hyperframework.error_handler.logger.class', ''
        );
        if ($loggerClass !== '') {
            if (class_exists($loggerClass) === false) {
                throw new ClassNotFoundException(
                    "Logger class '$class' does not exist, defined in "
                        . "'hyperframework.error_handler.logger.class'."
                );
            }
            return $loggerClass;
        }
    }

    protected function writeDefaultErrorLog() {
        if ($this->error instanceof Exception) {
            error_log('PHP ' . $this->getExceptionErrorLog());
        } else {
            error_log('PHP ' . $this->getError());
        }
    }

    protected function getLoggerMethod() {
        if ($this->shouldExit) {
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
        return $maps[$this->error->getCode()];
    }

    final protected function disableDefaultErrorReporting() {
        if ($this->shouldReportCompileWarning()) {
            error_reporting(E_COMPILE_WARNING);
        } else {
            if ($this->shouldDisplayErrors()) {
                ini_set('display_errors', '0');
            }
            if ($this->isDefaultErrorLogEnabled()) {
                ini_set('log_errors', '0');
            }
        }
    }

    final protected function shouldDisplayErrors() {
        return $this->shouldDisplayErrors;
    }

    final protected function getError() {
        return $this->error;
    }

    final protected function isLoggerEnabled() {
        return $this->isLoggerEnabled;
    }

    final protected function isDefaultErrorLogEnabled() {
        return $this->isDefaultErrorLogEnabled;
    }

    final protected function getErrorReportingBitmask() {
        return $this->errorReportingBitmask;
    }

    private function registerErrorHandler() {
        set_error_handler(
            function($type, $message, $file, $line) {
                return $this->handleError($type, $message, $file, $line);
            },
            $this->errorReportingBitmask
        );
    }

    private function registerExceptionHandler() {
        set_exception_handler(
            function($exception) {
                $this->handleException($exception);
            }
        );
    }

    private function registerShutdownHandler() {
        register_shutdown_function(
            function() {
                $this->handleShutdown();
            }
        );
    }

    private function handleException($exception) {
        $this->enableDefaultErrorReporting();
        $this->handle($exception);
    }

    private function handleError($type, $message, $file, $line) {
        $this->enableDefaultErrorReporting();
        $trace = null;
        $sourceTraceStartIndex = 2;
        $calledFunctionFile = null;
        $calledFunctionLine = null;
        if ($type === E_WARNING || $type === E_RECOVERABLE_ERROR) {
            $trace = debug_backtrace();
            if (isset($trace[2]) && isset($trace[2]['file'])) {
                $suffix = ', called in ' . $trace[2]['file']
                    . ' on line ' . $trace[2]['line'] . ' and defined';
                if (substr($message, -strlen($suffix)) === $suffix) {
                    $message =
                        substr($message, 0, strlen($message) - strlen($suffix));
                    $sourceTraceStartIndex = 3;
                    $calledFunctionFile = $file;
                    $calledFunctionLine = $line;
                    $file = $trace[2]['file'];
                    $line = $trace[2]['line'];
                }
            }
        }
        if ($calledFunctionFile !== null) {
            $message .= " (defined in " . $calledFunctionFile
                . ':' . $calledFunctionLine . ")";
        }
        if ($trace === null) {
            $trace = debug_backtrace();
        }
        $trace = array_slice($trace, $sourceTraceStartIndex);
        $error = new Error($type, $message, $file, $line, $trace);
        return $this->handle($error, true);
    }

    private function handleShutdown() {
        $this->isShutdownStarted = true;
        $this->enableDefaultErrorReporting(
            error_reporting() | ($this->getErrorReportingBitmask() & (
                E_ERROR | E_PARSE | E_CORE_ERROR
                    | E_COMPILE_ERROR | E_COMPILE_WARNING
            ))
        );
        $error = error_get_last();
        if ($error === null
            || $error['type'] & $this->getErrorReportingBitmask() === 0
        ) {
            return;
        }
        if (in_array($error['type'], [
            E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR
        ])) {
            $error = new FatalError(
                $error['type'], $error['message'],
                $error['file'], $error['line']
            );
            $this->enableDefaultErrorReporting();
            $this->handle($error, true);
        }
    }

    private function handle($error, $isError = false) {
        if ($this->error !== null) {
            if ($isError === false) {
                throw $error;
            }
            return false;
        }
        if ($error instanceof Error
            && $error instanceof FatalError === false
            && ($error->getCode() & (E_USER_ERROR | E_RECOVERABLE_ERROR)) === 0
        ) {
            $this->shouldExit = false;
        } else {
            $this->shouldExit = true;
        }
        $this->error = $error;
        $this->writeLog();
        if ($this->shouldExit === false) {
            if ($this->shouldDisplayErrors()) {
                $this->displayError();
            }
            $this->error = null;
            $this->disableDefaultErrorReporting();
            return;
        }
        $this->displayFatalError();
        if ($this->isShutdownStarted) {
            return;
        }
        ExitHelper::exitScript(1);
    }

    private function enableDefaultErrorReporting(
        $errorReportingBitmask = null
    ) {
        if ($errorReportingBitmask !== null) {
            error_reporting($errorReportingBitmask);
        } elseif ($this->shouldReportCompileWarning()) {
            error_reporting($this->getErrorReportingBitmask());
        }
        if ($this->shouldReportCompileWarning() === false) {
            if ($this->shouldDisplayErrors()) {
                ini_set('display_errors', '1');
            }
            if ($this->isDefaultErrorLogEnabled()) {
                ini_set('log_errors', '1');
            }
        }
    }

    private function shouldReportCompileWarning() {
        if ($this->shouldReportCompileWarning === null) {
            $this->shouldReportCompileWarning =
            ($this->getErrorReportingBitmask() & E_COMPILE_WARNING) !== 0;
        }
        return $this->shouldReportCompileWarning;
    }

    private function getExceptionErrorLog() {
        return 'Fatal error:  Uncaught ' . $this->error . PHP_EOL
            . '  thrown in ' . $this->error->getFile() . ' on line '
            . $this->error->getLine();
    }
}
