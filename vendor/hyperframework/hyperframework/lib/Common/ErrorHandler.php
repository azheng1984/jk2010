<?php
namespace Hyperframework\Common;

use Exception;
use Hyperframework\Logging\Logger;
use Hyperframework\Logging\LogLevel;

class ErrorHandler {
    private $error;

    public function run() {
        $this->registerErrorHandler();
        $this->registerExceptionHandler();
        $this->registerShutdownHandler();
    }

    protected function writeLog() {
        $isLoggerEnabled = Config::getBoolean(
            'hyperframework.error_handler.enable_logger', false
        );
        if ($isLoggerEnabled === false) {
            return;
        }
        $logLevel = $this->getLogLevel();
        $callback = function() {
            return $this->getLog();
        };
        $loggerClass = $this->getCustomLoggerClass();
        if ($loggerClass === null) {
            Logger::log($logLevel, $callback);
        } else {
            $loggerClass::log($logLevel, $callback);
        }
    }

    protected function getLogLevel() {
        if ($this->error instanceof Error) {
            $map = [
                E_DEPRECATED        => LogLevel::NOTICE,
                E_USER_DEPRECATED   => LogLevel::NOTICE,
                E_STRICT            => LogLevel::NOTICE,
                E_NOTICE            => LogLevel::NOTICE,
                E_USER_NOTICE       => LogLevel::NOTICE,
                E_WARNING           => LogLevel::WARNING,
                E_USER_WARNING      => LogLevel::WARNING,
                E_COMPILE_WARNING   => LogLevel::WARNING,
                E_CORE_WARNING      => LogLevel::WARNING,
                E_RECOVERABLE_ERROR => LogLevel::FATAL,
                E_USER_ERROR        => LogLevel::FATAL,
                E_ERROR             => LogLevel::FATAL,
                E_PARSE             => LogLevel::FATAL,
                E_COMPILE_ERROR     => LogLevel::FATAL,
                E_CORE_ERROR        => LogLevel::FATAL
            ];
            return $map[$this->error->getSeverity()];
        }
        return LogLevel::FATAL;
    }

    protected function displayError() {
    }

    final protected function getError() {
        return $this->error;
    }

    private function registerExceptionHandler() {
        set_exception_handler(
            function($exception) {
                $this->handleException($exception);
            }
        );
    }

    private function registerErrorHandler() {
        set_error_handler(
            function($type, $message, $file, $line) {
                return $this->handleError($type, $message, $file, $line);
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
        $this->handle($exception);
        throw $exception;
    }

    private function handleError($type, $message, $file, $line) {
        $errorThrowingBitmask = Config::getInt(
            'hyperframework.error_handler.error_throwing_bitmask'
        );
        if ($errorThrowingBitmask === null) {
            $errorThrowingBitmask =
                E_ALL & ~(E_DEPRECATED | E_USER_DEPRECATED);
        }
        if (($type & $errorThrowingBitmask) === 0) {
            return false;
        }
        $trace = null;
        $sourceTraceStartIndex = 2;
        if ($type === E_WARNING || $type === E_RECOVERABLE_ERROR) {
            $trace = debug_backtrace();
            if (isset($trace[2]) && isset($trace[2]['file'])) {
                $suffix = ', called in ' . $trace[2]['file']
                    . ' on line ' . $trace[2]['line'] . ' and defined';
                if (substr($message, -strlen($suffix)) === $suffix) {
                    $message =
                        substr($message, 0, strlen($message) - strlen($suffix))
                            . " (defined in $file:$line)";
                    $file = $trace[2]['file'];
                    $line = $trace[2]['line'];
                    $sourceTraceStartIndex = 3;
                }
            }
        }
        $error = new ErrorException(
            $type, $message, $file, $line, $sourceTraceStartIndex
        );
        return $this->handle($error, true);
    }

    private function handleShutdown() {
        $error = error_get_last();
        if ($error === null || $error['type'] & error_reporting() === 0) {
            return;
        }
        if (in_array($error['type'], [
            E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR
        ])) {
            $error = new Error(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
            $this->handle($error);
        }
    }

    private function handle($error, $shouldThrowError = false) {
        if ($this->error !== null) {
            return false;
        }
        if ($shouldThrowError) {
            throw $error;
        }
        $this->error = $error;
        $this->writeLog();
        $this->displayError();
    }

    private function getLog() {
        if ($this->error instanceof Exception) {
            $log = 'Fatal error:  Uncaught ' . $this->error . PHP_EOL
                . '  thrown in ' . $this->error->getFile() . ' on line '
                . $this->error->getLine();
        } else {
            $log = $this->getError();
        }
        $log = 'PHP ' . $log;
        $maxLength = Config::getInt(
            'hyperframework.error_handler.max_log_length', 1024
        );
        if ($maxLength > 0) {
            return substr($log, 0, $maxLength);
        }
        return $log;
    }

    private function getCustomLoggerClass() {
        $loggerClass = Config::getString(
            'hyperframework.error_handler.logger_class', ''
        );
        if ($loggerClass !== '') {
            if (class_exists($loggerClass) === false) {
                throw new ClassNotFoundException(
                    "Logger class '$class' does not exist, set using config "
                        . "'hyperframework.error_handler.logger_class'."
                );
            }
            return $loggerClass;
        }
    }
}
