<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParsingException extends Exception {
    private $isArgumentError;

    public function __construct(
        $message = '', $isArgumentError = false, $previous = null
    ) {
        $this->isArgumentError = $isArgumentError;
        parent::__construct($message, 0, $previous);
    }

    public function isArgumentError() {
        return $this->isArgumentError;
    }

    public function isOptionError() {
        return !$this->isArgumentError;
    }
}
