<?php
namespace Hyperframework\Common;

use Hyperframework\Logging\Logger;

class ErrorHandler {
    private $errorReportingBitmask;
    private $shouldReportCompileWarning;
    private $isDefaultErrorLogEnabled;
    private $isLoggerEnabled;
    private $shouldDisplayErrors;
    private $isShutdownStarted = false;
    private $shouldExit;
    private $exception;
    private $sourceType;

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
        set_error_handler(
            [$this, 'handleError'], $this->errorReportingBitmask
        );
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
        $this->disableDefaultErrorReporting();
    }

    final public function handleException($exception) {
        $this->enableDefaultErrorReporting();
        $this->handle($exception);
    }

    final public function handleError(
        $type, $message, $file, $line, array $context
    ) {
        $this->enableDefaultErrorReporting();
        $shouldThrow = false;
        if ($this->exception === null) {
            $errorThrowingBitmask = Config::getInt(
                'hyperframework.error_handler.error_throwing_bitmask'
            );
            if ($errorThrowingBitmask === null) {
                $errorThrowingBitmask =
                    E_ALL & ~(E_STRICT | E_DEPRECATED | E_USER_DEPRECATED);
            }
            if (($type & $errorThrowingBitmask) !== 0) {
                $shouldThrow = true;
            }
        }
        $error = null;
        if ($type === E_WARNING || $type === E_RECOVERABLE_ERROR) {
            $trace = debug_backtrace();
            if (isset($trace[1]) && isset($trace[1]['file'])) {
                $suffix = ', called in ' . $trace[1]['file']
                    . ' on line ' . $trace[1]['line'] . ' and defined';
                if (substr($message, -strlen($suffix)) === $suffix) {
                    $message =
                        substr($message, 0, strlen($message) - strlen($suffix));
                    $error = new ArgumentErrorException(
                        $message, $type, $trace[1]['file'], $trace[1]['line'],
                        $file, $line, 1, $context
                    );
                }
            }
        }
        if ($error === null) {
            $error = new ErrorException(
                $message, $type, $file, $line, 1, $context
            );
        }
        return $this->handle($error, true, $shouldThrow);
    }

    final public function handleShutdown() {
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
        $error = new ErrorException(
            $error['message'], $error['type'], $error['file'],
            $error['line'], null
        );
        if ($error->isFatal()) {
            $this->enableDefaultErrorReporting();
            $this->handle($error, true);
        }
    }

    protected function writeLog() {
        if ($this->isLoggerEnabled()) {
            $exception = $this->exception;
            $name = null;
            $data = [];
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
            if ($this->sourceType === 'exception') {
                $name = 'php_exception';
                $data['class'] = get_class($exception);
            } else {
                $name = 'php_error';
                $data['type'] = $exception->getSeverityAsConstantName();
            }
            if ($this->sourceType !== 'fatal_error') {
                $shouldLogTrace = Config::getBoolean(
                    'hyperframework.error_handler.logger.log_stack_trace', false
                );
                if ($shouldLogTrace) {
                    $data['stack_trace'] = [];
                    foreach ($exception->getTrace() as $item) {
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
                        $data['stack_trace'][] = $trace;
                    }
                }
            }
            $method = $this->getLoggerMethod();
            Logger::$method([
                'name' => $name,
                'message' => $exception->getMessage(),
                'data' => $data
            ]);
        } elseif ($this->isDefaultErrorLogEnabled()) {
            $this->writeDefaultErrorLog();
        }
    }

    protected function writeDefaultErrorLog() {
        if ($this->sourceType === 'exception') {
            error_log('PHP ' . $this->getExceptionErrorLog());
        } else {
            error_log('PHP ' . $this->getErrorLog());
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
        return $maps[$this->exception->getSeverity()];
    }

    protected function displayError() {
        $exception = $this->exception;
        if (ini_get('xmlrpc_errors') === '1') {
            $code = ini_get('xmlrpc_error_number');
            echo '<?xml version="1.0"?', '><methodResponse>',
                '<fault><value><struct><member><name>faultCode</name>',
                '<value><int>', $code, '</int></value></member><member>',
                '<name>faultString</name><value><string>';
            if ($this->sourceType === 'exception') {
                $message = $this->getExceptionErrorLog();
            } else {
                $message = $this->getErrorLog();
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
            if ($this->sourceType === 'exception') {
                echo $this->getExceptionErrorLog();
            } else {
                echo $this->getErrorLog();
            }
            echo PHP_EOL, $suffix;
            return;
        }
        echo $prefix, '<br />', PHP_EOL, '<b>';
        if ($this->sourceType !== 'exception') {
            echo $exception->getSeverityAsString(), '</b>:  ';
            if (ini_get('docref_root') !== '') {
                echo $exception->getMessage();
            } else {
                echo htmlspecialchars(
                    $exception->getMessage(),
                    ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
                );
            }
        } else {
            echo 'Fatal error</b>:  Uncaught ', htmlspecialchars(
                $this->exception,
                ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            ), PHP_EOL, '  thrown';
        }
        echo ' in <b>', htmlspecialchars(
            $this->exception->getFile(),
            ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
        ), '</b> on line <b>', $this->exception->getLine(),
        '</b><br />', PHP_EOL, $suffix;
    }

    protected function displayFatalError() {
        $this->displayError();
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

    final protected function getException() {
        return $this->exception;
    }

    final protected function getSourceType() {
        if ($this->exception === null) {
            throw new InvalidOperationException('No error or exception.');
        }
        return $this->sourceType;
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

    private function handle(
        $exception, $isError = false, $shouldThrow = false
    ) {
        if ($this->exception !== null) {
            if ($isError === false) {
                throw $exception;
            }
            return false;
        }
        if ($isError && $shouldThrow) {
            $this->disableDefaultErrorReporting();
            throw $exception;
        }
        if ($isError && $exception->isFatal() === false) {
            $this->shouldExit = false;
        } else {
            $this->shouldExit = true;
        }
        $this->exception = $exception;
        if ($isError) {
            if ($exception->isFatal()) {
                $this->sourceType = 'fatal_error';
            } else {
                $this->sourceType = 'error';
            }
        } else {
            $this->sourceType = 'exception';
        }
        $this->writeLog();
        if ($this->shouldExit === false) {
            if ($this->shouldDisplayErrors()) {
                $this->displayError();
            }
            $this->exception = null;
            $this->disableDefaultErrorReporting();
            return;
        }
        $this->displayFatalError();
        if ($this->isShutdownStarted) {
            return;
        }
        exit(1);
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
        return 'Fatal error:  Uncaught ' . $this->exception . PHP_EOL
        . '  thrown in ' . $this->exception->getFile() . ' on line '
            . $this->exception->getLine();
    }

    private function getErrorLog() {
        $error = $this->exception;
        $result = $error->getSeverityAsString();
        if ($error instanceof ArgumentErrorException) {
            return $result . ':  ' . $error->getMessage() . ' called in '
                . $error->getFile() . ' on line ' . $error->getLine()
                . ' and defined in ' . $error->getFunctionDefinitionFile()
                . ' on line ' . $error->getFunctionDefinitionLine();
        }
        return $result . ':  ' . $error->getMessage() . ' in '
            . $error->getFile() . ' on line ' . $error->getLine();
    }
}
