<?php
namespace Tc;

class CommandCollection {
    public function execute($options) {
        $argv = $_SERVER['argv'];
        print_r($argv);
        echo 'Usage: tc [options] command' . PHP_EOL;
    }
}
