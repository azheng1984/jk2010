<?php
namespace Hyperframework\Cli;

class OptionRouter {
    public static function route($options, $config) {
        while (CommandOptionRouter::route([
            '-x' => function() {
            }
        ], [
            '-y' => function() {
            }
        ]));

        $result = OptionRouter::routeAll($options, [
            '-x' => function($router) {
                $router->quit();
            }
        ], [
            '-y' => function($router) {
                $router->quit();
            }
        ]);

        $result['name'] = '-x';
        $result['return'] = $xxx;

        return false;
    }

    public static function routeAll($options, $config) {
    }
}
