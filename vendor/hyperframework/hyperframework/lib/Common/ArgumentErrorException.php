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
        $previous = null
    ) {
        parent::__construct(
            $message, $severity, $file, $line,
            $sourceStackFrameStartingPosition, $context, $previous
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
