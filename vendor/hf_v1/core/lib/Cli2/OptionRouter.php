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

        $result = OptionRouter::run([
            '-x' => function() {
            }
        ], [
            '-y' => function() {
            }
        ]);

        if ($result === false) {
        }

        return false;
    }
}
