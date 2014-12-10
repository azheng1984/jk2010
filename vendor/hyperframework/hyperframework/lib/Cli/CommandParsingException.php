<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParsingException extends Exception {
    private $errorType;

    public function __construct(
        $message = '', $errorType = '', $previous = null
    ) {
        $this->errorType = $errorType;
        parent::__construct($message, 0, $previous);
    }

    public function getErrorType() {
        return $this->errorType;
    }
}
