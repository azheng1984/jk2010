<?php
namespace Hyperframework\Common;

class ArgumentError extends Error {
    private $functionDefinitionFile;
    private $functionDefinitionLine;

    public function __construct(
        $message,
        $severity,
        $file,
        $line,
        array $trace,
        $functionDefinitionFile,
        $functionDefinitionLine,
        array $context,
        $previous = null
    ) {
        parent::__construct(
            $message, $severity, $file, $line,
            $trace, $context, $previous
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
    }
}
