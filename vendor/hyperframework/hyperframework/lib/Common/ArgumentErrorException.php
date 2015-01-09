<?php
namespace Hyperframework\Common;

class ArgumentErrorException extends ErrorException {
    private $functionDefinitionFile;
    private $functionDefinitionLine;

    public function __construct(
        $message = '',
        $severity = E_ERROR,
        $file = __FILE__,
        $line = __LINE__,
        $functionDefinitionFile = null,
        $functionDefinitionLine = null,
        $sourceStackFrameStartingPosition = 0,
        array $context = null,
        $shouldThrow = false,
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
