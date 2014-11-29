<?php
namespace Hyperframework\Cli;

class HelpView {
    public function render() {
        echo 'Usage: command_name [options] command' . PHP_EOL;
    }

    protected function renderUsage() {
    }
}
