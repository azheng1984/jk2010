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

        $result = OptionRouter::runAll($options, [
            '-x' => function($router) {
                $router->quit();
            }
        ], [
            '-y' => function($router) {
                $router->quit();
            }
        ]);

        $result['option'] = '-x';
        $result['return'] = $xxx;

        return false;
    }
}
