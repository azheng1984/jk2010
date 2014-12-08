<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParsingException extends Exception {
    public function getSubcommand() {
    }

    public function __toString() {
        return $this->getMessage();
    }
}
