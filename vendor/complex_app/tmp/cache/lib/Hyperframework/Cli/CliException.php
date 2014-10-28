<?php
namespace Hyperframework\Cli;

class CliException extends \Exception {
    public function __construct($message, $code = 1) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return $this->getMessage();
    }
}
