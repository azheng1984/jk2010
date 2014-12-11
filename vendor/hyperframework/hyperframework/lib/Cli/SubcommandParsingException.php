<?php
namespace Hyperframework\Cli;

class SubcommandParsingException extends CommandParsingException {
    private $subcommand;

    public function __construct(
        $subcommand, $message = '', $errorType = '', $previous = null
    ) {
        parent::__construct($message, $errorType, $previous);
        $this->subcommand = $subcommand;
    }

    public function getSubcommand() {
        return $this->subcommand;
    }
}
