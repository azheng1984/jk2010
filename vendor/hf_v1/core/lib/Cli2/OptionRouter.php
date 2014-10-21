<?php
namespace Hyperframework\Cli;

class OptionRouter {
    public static function run($config) {
        while (OptionRouter::run([
            '-x' => function() {
            }
        ], [
            '-y' => function() {
            }
        ]));
        return false;
    }
}
