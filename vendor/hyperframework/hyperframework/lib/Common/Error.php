<?php
namespace Hyperframework\Common;

class Error {
    private $message;
    private $severity;
    private $file;
    private $line;

    public function __construct($severity, $message, $file, $line) {
        $this->severity = $severity;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getSeverity() {
        return $this->severity;
    }

    public function getSeverityAsString() {
        return ErrorTypeHelper::convertToString($this->getSeverity());
    }

    public function getSeverityAsConstantName() {
        return ErrorTypeHelper::convertToConstantName($this->getSeverity());
    }

    public function getFile() {
        return $this->file;
    }

    public function getLine() {
        return $this->line;
    }

    public function __toString() {
        return $this->getSeverityAsString() . ':  ' . $this->getMessage()
            . ' in ' . $this->getFile() . ' on line ' . $this->getLine();
    }
}
