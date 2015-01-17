<?php
namespace Hyperframework\Common;

use Hyperframework\Logging\Logger;

class ErrorHandler {
    private $source;
    private $isError;
    private $isLoggerEnabled;
    private $errorReportingBitmask;
    private $isShutdownStarted = false;
    private $shouldExit;
    private $shouldDisplayErrors;
    private $shouldReportCompileWarning;
    private $isDefaultErrorLogEnabled;

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
        $this->errorReportingBitmask = error_reporting();
        $this->shouldDisplayErrors = ini_get('display_errors') === '1';
    }

    public function run() {
        set_error_handler(
            [$this, 'handleError'], $this->errorReportingBitmask
        );
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleFatalError']);
        $this->disableDefaultErrorReporting();
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

    private function disableDefaultErrorReporting() {
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

    private function shouldReportCompileWarning() {
        if ($this->shouldReportCompileWarning === null) {
            $this->shouldReportCompileWarning =
            ($this->getErrorReportingBitmask() & E_COMPILE_WARNING) !== 0;
        }
        return $this->shouldReportCompileWarning;
    }

    protected function shouldDisplayErrors() {
        return $this->shouldDisplayErrors;
    }

    final public function handleException($exception) {
        if (error_reporting() === 0) {
            return;
        }
        $this->enableDefaultErrorReporting();
        $this->handle($exception);
    }

    final public function handleError(
        $type, $message, $file, $line, array $context
    ) {
        if (error_reporting() === 0) {
            return;
        }
        $this->enableDefaultErrorReporting();
        $shouldThrow = false;
        if ($this->source === null) {
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
                        $file, $line, 1, $context, $shouldThrow
                    );
                }
            }
        }
        if ($error === null) {
            $error = new ErrorException(
                $message, $type, $file, $line, 1, $context, $shouldThrow
            );
        }
        return $this->handle($error, true);
    }

    final public function handleFatalError() {
        $this->isShutdownStarted = true;
        if (error_reporting() === 0) {
            return;
        }
        $this->enableDefaultErrorReporting(
            error_reporting() | ($this->getErrorReportingBitmask() & (
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
            $error['line'], null
        );
        if ($error->isFatal()) {
            $this->enableDefaultErrorReporting();
            $this->handle($error, true);
        }
    }

    private function handle($source, $isError = false) {
        if ($this->source !== null) {
            if ($isError === false) {
                throw new $source;
            }
            return false;
        }
        if ($isError && $source->shouldThrow()) {
            throw $source;
        }
        if ($isError && $source->isFatal() === false) {
            $this->shouldExit = false;
        } else {
            $this->shouldExit = true;
        }
        $this->source = $source;
        $this->isError = $source instanceof ErrorException;
        $this->writeLog();
        if ($this->shouldExit === false) {
            if ($this->shouldDisplayErrors()) {
                $this->displayError();
            }
            $this->source = null;
            $this->disableDefaultErrorReporting();
            return;
        }
        $this->displayFatalError();
        if ($this->isShutdownStarted) {
            return;
        }
        exit(1);
    }

    protected function displayFatalError() {
        $this->displayError();
    }

    protected function writeLog() {
        if ($this->isLoggerEnabled()) {
            $source = $this->source;
            $name = null;
            $data = [];
            $data['file'] = $source->getFile();
            $data['line'] = $source->getLine();
            if ($this->isError === false) {
                $name = 'php_exception';
                $data['class'] = get_class($source);
            } else {
                if ($this->source->shouldThrow()) {
                    $name = 'php_error_exception';
                } else {
                    $name = 'php_error';
                    $data['type'] = $source->getSeverityAsString();
                }
            }
            if ($this->isError() === false || $source->isFatal() === false) {
                $shouldLogTrace = Config::getBoolean(
                    'hyperframework.error_handler.logger.log_stack_trace', false
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
            $method = $this->getLoggerMethod();
            Logger::$method([
                'name' => $name,
                'message' => $source->getMessage(),
                'data' => $data
            ]);
        }
        if ($this->isDefaultErrorLogEnabled()) {
            $this->writeDefaultErrorLog();
        }
    }

    protected function writeDefaultErrorLog() {
        if ($this->isError) {
            error_log('PHP ' . $this->getErrorLog());
        } else {
            error_log('PHP ' . $this->getExceptionErrorLog());
        }
    }

    private function getExceptionErrorLog() {
        return 'Fatal error:  Uncaught ' . $this->source . PHP_EOL
            . '  thrown in ' . $this->source->getFile() . ' on line '
            . $this->source->getLine();
    }

    private function getErrorLog() {
        $error = $this->source;
        if ($error->shouldThrow() === true) {
            $result = 'Fatal error';
        } else {
            $result = $this->convertErrorTypeForOutput($error->getSeverity());
        }
        if ($error instanceof ArgumentErrorException) {
            return $result . ':  ' . $error->getMessage() . ' called in '
                . $error->getFile() . ' on line ' . $error->getLine()
                . ' and defined in ' . $error->getFunctionDefinitionFile()
                . ' on line ' . $error->getFunctionDefinitionLine();
        }
        return $result . ':  ' . $error->getMessage() . ' in '
            . $error->getFile() . ' on line ' . $error->getLine();
    }

    private function convertErrorTypeForOutput($type) {
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
        return $maps[$this->source->getSeverity()];
    }

    final protected function getSource() {
        return $this->source;
    }

    final protected function isError() {
        if ($this->source === null) {
            throw new InvalidOperationException('No error or exception.');
        }
        return $this->isError;
    }

    protected function isLoggerEnabled() {
        return $this->isLoggerEnabled;
    }

    protected function isDefaultErrorLogEnabled() {
        return $this->isDefaultErrorLogEnabled;
    }

    protected function getErrorReportingBitmask() {
        return $this->errorReportingBitmask;
    }

    protected function displayError() {
        $source = $this->source;
        if (ini_get('xmlrpc_errors') === '1') {
            $code = ini_get('xmlrpc_error_number');
            echo '<?xml version="1.0"?><methodResponse>',
                '<fault><value><struct><member><name>faultCode</name>',
                '<value><int>', $code, '</int></value></member><member>',
                '<name>faultString</name><value><string>';
            if ($this->isError) {
                $message = $this->getErrorLog();
            } else {
                $message = $this->getExceptionErrorLog();
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
            if ($this->isError === false) {
                echo $this->getExceptionErrorLog();
            } else {
                echo $this->getErrorLog();
            }
            echo PHP_EOL, $suffix;
            return;
        }
        echo $prefix, '<br />', PHP_EOL, '<b>';
        if ($this->isError) {
            if ($source->shouldThrow() === true) {
                echo 'Fatal error';
            } else {
                echo $this->convertErrorTypeForOutput($source->getSeverity());
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
                $this->source,
                ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            ), PHP_EOL, '  thrown';
        }
        echo ' in <b>', htmlspecialchars(
                $this->source->getFile(),
                ENT_NOQUOTES | ENT_HTML401 | ENT_SUBSTITUTE
            ), '</b> on line <b>', $this->source->getLine(),
            '</b><br />', PHP_EOL, $suffix;
    }
}
