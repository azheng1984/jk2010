<?php
namespace Hyperframework\Common;

class Error {
    private $message;
    private $severity;
    private $file;
    private $line;
    private $context;
    private $trace;

    public function __construct(
        $severity,
        $message,
        $file,
        $line,
        array $trace = null,
        array $context = null,
    ) {
        $this->severity =  $severity;
        $this->message = $message;
        $this->file =  $file;
        $this->line = $line;
        $this->context = $context;
        $this->trace = $trace;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getSeverity() {
        return $this->severity;
    }

    public function getSeverityAsString() {
        ErrorTypeHelper::convertToString($this->getSeverity());
    }

    public function getSeverityAsConstantName() {
        ErrorTypeHelper::convertToConstantName($this->getSeverity());
    }


    public function getFile() {
        return $this->file;
    }

    public function getLine() {
        return $this->line;
    }

    public function getTrace() {
        return $this->trace;
    }

    public function getTraceAsString() {
        $trace = $this->getSourceTrace();
        if ($trace === null) {
            return '';
        }
        return StackTraceFormatter::format($trace);
    }

    public function getContext() {
        return $this->context;
    }

    public function __toString() {
        return $this->getSeverityAsString() . ':  ' . $this->getMessage()
            . ' in ' . $this->getFile() . ' on line ' . $this->getLine();
    }
}
