<?php
namespace Hyperframework\Cli;

class OptionDispatcher {
    public static function ($options, $config) {

        while (OptionDispatcher::dispatch([
            '-x' => function() {
            }
        ], [
            '-y' => function() {
            }
        ]));

        OptionDispatcher::dispatchAll(['-x' => function() {
        }]);

        isset($options['-x']);
        $options = $options->toArray();

        if (isset($options['-x'])) {
        }

        $options->toArray();

        $options->dispatchAll();

        $result = OptionDispatcher::dispatchAll($options, [
            '-x' => function($ctx) {
                $ctx->stop();
            }
        ], [
            '-y' => function($ctx) {
                $ctx->stop();
            }
        ]);

        $result['name'] = '-x';
        $result['return'] = $xxx;

        return false;
    }

    public static function dispatchAll($options, $config) {
    }
}
