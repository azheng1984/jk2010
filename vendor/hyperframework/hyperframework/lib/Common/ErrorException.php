<?php
namespace Hyperframework\Common;

use ErrorException as Base;

class ErrorException extends Base {
    private $sourceTrace;
    private $sourceTraceStartIndex;

    public function __construct(
        $severity, $message, $file, $line, $sourceTraceStartIndex
    ) {
        parent::__construct($message, 0, $severity, $file, $line);
        $this->sourceTraceStartIndex = $sourceTraceStartIndex;
    }

    public function getSeverityAsString() {
        return ErrorTypeHelper::convertToString($this->getSeverity());
    }

    public function getSeverityAsConstantName() {
        return ErrorTypeHelper::convertToConstantName($this->getSeverity());
    }

    public function getSourceTrace() {
        if ($this->sourceTrace === null) {
            if ($this->sourceTraceStartIndex === 0) {
                $this->sourceTrace = $this->getTrace();
            } else {
                $this->sourceTrace = array_slice(
                    $this->getTrace(), $this->sourceTraceStartIndex
                );
            }
        }
        return $this->sourceTrace;
    }

    public function getSourceTraceAsString() {
        $trace = $this->getSourceTrace();
        return StackTraceFormatter::format($trace);
    }

    public function __toString() {
        $result = "exception '" . get_called_class() . "'";
        $message = (string)$this->getMessage();
        if ($message !== '') {
            $result .= " with message '" . $message . "'";
        }
        $result .= ' in ' . $this->getFile() . ':' . $this->getLine()
            . PHP_EOL . 'Stack trace:' . PHP_EOL
            . $this->getSourceTraceAsString();
        return $result;
    }
}
