<?php
namespace Hyperframework\Common;

use ErrorException as Base;

class ErrorException extends Base {
    private $context;
    private $sourceStackTrace;
    private $sourceStackFrameStartingPosition;
    private $isFatal;

    public function __construct(
        $message,
        $severity,
        $file,
        $line,
        $sourceStackFrameStartingPosition,
        array $context = null,
        $previous = null
    ) {
        parent::__construct(
            $message, 0, $severity, $file, $line, $previous
        );
        $this->sourceStackFrameStartingPosition =
            $sourceStackFrameStartingPosition;
        $this->context = $context;
    }

    public function getContext() {
        return $this->context;
    }

    public function isFatal() {
        if ($this->isFatal === null) {
            $this->isFatal = in_array($this->getSeverity(), [
                E_ERROR,
                E_PARSE,
                E_CORE_ERROR,
                E_COMPILE_ERROR
            ]);
        }
        return $this->isFatal;
    }

    public function getSeverityAsString() {
        switch ($this->getSeverity()) {
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

    public function getSeverityAsConstantName() {
        switch ($this->getSeverity()) {
            case E_STRICT:            return 'E_STRICT';
            case E_DEPRECATED:        return 'E_DEPRECATED';
            case E_USER_DEPRECATED:   return 'E_USER_DEPRECATED';
            case E_NOTICE:            return 'E_NOTICE';
            case E_ERROR:             return 'E_ERROR';
            case E_USER_NOTICE:       return 'E_USER_NOTICE';
            case E_USER_ERROR:        return 'E_USER_ERROR';
            case E_WARNING:           return 'E_WARNING';
            case E_USER_WARNING:      return 'E_USER_WARNING';
            case E_COMPILE_WARNING:   return 'E_COMPILE_WARNING';
            case E_CORE_WARNING:      return 'E_CORE_WARNING';
            case E_RECOVERABLE_ERROR: return 'E_RECOVERABLE_ERROR';
            case E_PARSE:             return 'E_PARSE';
            case E_COMPILE_ERROR:     return 'E_COMPILE_ERROR';
            case E_CORE_ERROR:        return 'E_CORE_ERROR';
        }
    }

    public function getSourceTrace() {
        if ($this->sourceStackTrace === null) {
            if ($this->sourceStackFrameStartingPosition !== null) {
                if ($this->sourceStackFrameStartingPosition === 0) {
                    $this->sourceStackTrace = $this->getTrace();
                } else {
                    $this->sourceStackTrace = array_slice(
                        $this->getTrace(),
                        $this->sourceStackFrameStartingPosition
                    );
                }
            }
            if ($this->sourceStackTrace === null) {
                $this->sourceStackTrace = false;
            }
        }
        if ($this->sourceStackTrace === false) {
            return;
        }
        return $this->sourceStackTrace;
    }

    public function getSourceTraceAsString() {
        $trace = $this->getSourceTrace();
        if ($trace === null) {
            return '';
        }
        return StackTraceFormatter::format($trace);
    }

    public function __toString() {
        $message = 'exception \'' . get_called_class(). '\' with message \''
            . $this->getMessage() . '\' in ' . $this->getFile() . ':'
            . $this->getLine();
        if ($this->isFatal() === false) {
            $message .= PHP_EOL . 'Stack trace:' . PHP_EOL
                . $this->getSourceTraceAsString();
        }
        return $message;
    }
}
