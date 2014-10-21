<?php
namespace Hyperframework\Cli;

class OptionDispatcher {
    public static function dispatch($options, $config) {
        while (OptionDispatcher::dispatch($options, [
            '-x' => function() {
            }
        ], [
            '-y' => function() {
            }
        ]));

        $result = OptionDispatcher::dispatchAll($options, [
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

    public static function dispatchAll($options, $config) {
    }
}
