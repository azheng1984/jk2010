<?php
namespace Tc;

class Command {
    public function execute() {
        $argv = $_SERVER['argv'];
        print_r($argv);
        echo 'Usage: tc [options] command' . PHP_EOL;
    }
}
