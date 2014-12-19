<?php
namespace Hyperframework\Cli;

class SubcommandParsingException extends CommandParsingException {
    private $subcommand;

    public function __construct(
        $subcommand, $message = '', $code = 0, $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->subcommand = $subcommand;
    }

    public function getSubcommand() {
        return $this->subcommand;
    }
}
