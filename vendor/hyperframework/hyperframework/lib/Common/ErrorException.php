<?php
namespace Hyperframework\Common;

use ErrorException as Base;

class ErrorException extends Base {
    private $context;
    private $sourceStackTrace;
    private $sourceStackFrameStartingPosition;
    private $shouldThrow;
    private $isFatal;

    public function __construct(
        $message,
        $severity,
        $file,
        $line,
        $sourceStackFrameStartingPosition,
        array $context = null,
        $shouldThrow = false,
        $previous = null
    ) {
        parent::__construct(
            $message, 0, $severity, $file, $line, $previous
        );
        $this->sourceStackFrameStartingPosition =
            $sourceStackFrameStartingPosition;
        $this->context = $context;
        $this->shouldThrow = $shouldThrow;
    }

    public function getContext() {
        return $this->context;
    }

    public function shouldThrow() {
        return $this->shouldThrow;
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
            case E_STRICT:            return 'strict';
            case E_DEPRECATED:        return 'deprecated';
            case E_USER_DEPRECATED:   return 'user deprecated';
            case E_NOTICE:            return 'notice';
            case E_ERROR:             return 'error';
            case E_USER_NOTICE:       return 'user notice';
            case E_USER_ERROR:        return 'user error';
            case E_WARNING:           return 'warning';
            case E_USER_WARNING:      return 'user warning';
            case E_COMPILE_WARNING:   return 'compile warning';
            case E_CORE_WARNING:      return 'core warning';
            case E_RECOVERABLE_ERROR: return 'recoverable error';
            case E_PARSE:             return 'parse error';
            case E_COMPILE_ERROR:     return 'compile error';
            case E_CORE_ERROR:        return 'core error';
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
            return 'NULL';
        }
        return StackTraceFormatter::format($trace);
    }

    public function __toString() {
        $message = 'exception \'' . get_called_class(). '\' with message \''
            . $this->getMessage() . '\' in ' . $this->getFile() . ':'
            . $this->getLine() . PHP_EOL
            . 'Stack trace:' . PHP_EOL
            . $this->getSourceTraceAsString();
        return $message;
    }
}
