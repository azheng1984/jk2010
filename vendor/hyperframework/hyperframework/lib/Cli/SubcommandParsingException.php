<?php
namespace Hyperframework\Cli;

class SubcommandParsingException extends CommandParsingException {
    private $subcommand;

    public function __construct(
        $subcommand, $message = '', $isArgumentError = false, $previous = null
    ) {
        parent::__construct($message, $isArgumentError, $previous);
        $this->subcommand = $subcommand;
    }

    public function getSubcommand() {
        return $this->subcommand;
    }
}
