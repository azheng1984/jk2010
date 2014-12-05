<?php
namespace Hyperframework\Cli;

class HelpView {
    private $app;

    //do not pass exception, use array instead
    public function __construct($app, $commandParsingException = null) {
        $this->app = $app;
    }

    public function render() {
        echo 'Usage: command_name [options] command' . PHP_EOL;
    }

    public function renderUsage() {
        //short version for command parsing exception
    }
}
