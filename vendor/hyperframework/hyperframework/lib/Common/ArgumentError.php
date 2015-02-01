<?php
namespace Hyperframework\Common;

class ArgumentError extends Error {
    private $functionDefinitionFile;
    private $functionDefinitionLine;

    public function __construct(
        $severity,
        $message,
        $file,
        $line,
        $functionDefinitionFile,
        $functionDefinitionLine,
        array $trace
    ) {
        parent::__construct(
            $severity, $message, $file, $line, $trace
        );
        $this->functionDefinitionLine = $functionDefinitionLine;
        $this->functionDefinitionFile = $functionDefinitionFile;
    }

    public function getFunctionDefinitionFile() {
        return $this->functionDefinitionFile;
    }

    public function getFunctionDefinitionLine() {
        return $this->functionDefinitionLine;
    }

    public function __toString() {
        return $this->getSeverityAsString() . ':  ' . $this->getMessage()
            . ' called in ' . $this->getFile() . ' on line ' . $this->getLine()
            . ' and defined in ' . $this->getFunctionDefinitionFile()
            . ' on line ' . $this->getFunctionDefinitionLine();
    }
}
