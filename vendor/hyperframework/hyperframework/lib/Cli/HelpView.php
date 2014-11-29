<?php
namespace Hyperframework\Cli;

class HelpView {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function render() {
        echo 'Usage: command_name [options] command' . PHP_EOL;
    }
}
