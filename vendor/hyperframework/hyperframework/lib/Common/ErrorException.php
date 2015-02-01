<?php
namespace Hyperframework\Common;

use ErrorException as Base;

class ErrorException extends Base {
    private $context;
    private $sourceStackTrace;
    private $sourceStackFrameStartingPosition;

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

    public function getSeverityAsString() {
        ErrorTypeHelper::convertToString($this->getSeverity());
    }

    public function getSeverityAsConstantName() {
        ErrorTypeHelper::convertToConstantName($this->getSeverity());
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
        $result = "exception '" . get_called_class() . "'";
        $message = (string)$this->getMessage();
        if ($message !== '') {
            $result .= " with message '" . $message . "'";
        }
        $result .= ' in ' . $this->getFile() . ':' . $this->getLine();
            . PHP_EOL . 'Stack trace:' . PHP_EOL
            . $this->getSourceTraceAsString();
        return $result;
    }
}
