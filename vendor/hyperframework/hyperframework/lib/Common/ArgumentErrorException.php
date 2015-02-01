<?php
namespace Hyperframework\Common;

class ArgumentErrorException extends ErrorException {
    private $functionDefinitionFile;
    private $functionDefinitionLine;

    public function __construct(
        $severity,
        $message,
        $file,
        $line,
        $functionDefinitionFile,
        $functionDefinitionLine,
        $sourceStackFrameStartingPosition,
        array $context
    ) {
        parent::__construct(
            $severity, $message, $file, $line,
            $sourceStackFrameStartingPosition, $context
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
}
