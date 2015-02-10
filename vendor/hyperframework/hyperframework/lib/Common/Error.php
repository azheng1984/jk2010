<?php
namespace Hyperframework\Common;

class Error {
    private $message;
    private $severity;
    private $file;
    private $line;
    private $trace;

    public function __construct(
        $code, $message, $file, $line, array $trace = null
    ) {
        $this->code =  $code;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
        $this->trace = $trace;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getCode() {
        return $this->code;
    }

    public function getCodeAsString() {
        return ErrorTypeHelper::convertToString($this->getCode());
    }

    public function getCodeAsConstantName() {
        return ErrorTypeHelper::convertToConstantName($this->getCode());
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
        $trace = $this->getTrace();
        if ($trace === null) {
            return '';
        }
        return StackTraceFormatter::format($trace);
    }

    public function __toString() {
        return $this->getCodeAsString() . ':  ' . $this->getMessage()
            . ' in ' . $this->getFile() . ' on line ' . $this->getLine();
    }
}
