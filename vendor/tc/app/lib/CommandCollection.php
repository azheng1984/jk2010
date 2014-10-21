<?php
namespace Tc;

class CommandCollection {
    public function execute($options) {
        $argv = $_SERVER['argv'];
        print_r($argv);
        echo 'Usage: tc [options] command' . PHP_EOL;
        OptionGroup::create('--version', function() {
        })->bind('--xxx', function() {
        })->bind('--xx', function() {
        });

        OptionGroup::run(['--version' => function() use() {
        }], ['--xx' => function() use() {
            OptionGroup::run(['--xxx' => function() {
            }], ['--xx' => function() {
                $this->processor();
            }], ['--xxx' => [$this, 'processor']]);
        }]);
    }
}
