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

        $result = OptionRouter::run($options, [
            '-x' => function() {
            }
        ], [
            '-y' => function() {
            }
        ]);

        $result['name'] = '-x';
        $result['return'] = $xxx;

        return false;
    }
}
