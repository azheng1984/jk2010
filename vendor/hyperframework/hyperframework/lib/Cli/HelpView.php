<?php
namespace Hyperframework\Cli;

class HelpView {
    public function render() {
    }

    protected function renderUsage() {
        echo 'Usage: command_name [options] command';
    }
}
