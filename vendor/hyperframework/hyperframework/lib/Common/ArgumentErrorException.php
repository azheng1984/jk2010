<?php
namespace Hyperframework\Common;

class ArgumentErrorException extends ErrorException {
    private $functionDefinitionFile;
    private $functionDefinitionLine;

    public function __construct(
        $message,
        $severity,
        $file,
        $line,
        $functionDefinitionFile,
        $functionDefinitionLine,
        $sourceStackFrameStartingPosition,
        array $context,
        $shouldThrow,
        $previous = null
    ) {
        parent::__construct(
            $message, $severity, $file, $line,
            $sourceStackFrameStartingPosition, $context, $shouldThrow, $previous
        );
        $this->functionDefinitionLine = $functionDefinitionLine;
        $this->functionDefinitionFile = $functionDefinitionFile;
    }

    public function getFunctionDefinitionLine() {
        return $this->functionDefinitionLine;
    }

    public function getFunctionDefinitionFile() {
        return $this->functionDefinitionFile;
    }
}
