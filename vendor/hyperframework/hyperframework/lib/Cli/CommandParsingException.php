<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParsingException extends Exception {
    private $isArgumentError;

    public function __construct(
        $message, $isArgumentError = false, $previous = null
    ) {
        $this->isArgumentError = false;
        parent::($message, 0, $previous);
    }

    public function isArgumentError() {
        return $this->isArgumentError;
    }

    public function isOptionError() {
        return !$this->isArgumentError;
    }
}
