<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParsingException extends Exception {
    private $subcommandName;
    
    public function __construct(
        $message = '', $subcommandName = null, $code = 0, $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->subcommandName = $subcommandName;
    }

    public function getSubcommandName() {
        return $this->subcommandName;
    }
}
