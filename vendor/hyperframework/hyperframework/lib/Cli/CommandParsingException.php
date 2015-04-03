<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParsingException extends Exception {
    private $subcommandName;

    /**
     * @param string $message
     * @param string $subcommandName
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(
        $message = '',
        $subcommandName = null,
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->subcommandName = $subcommandName;
    }

    /**
     * @return string
     */
    public function getSubcommandName() {
        return $this->subcommandName;
    }
}
