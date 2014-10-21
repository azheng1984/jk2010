<?php
namespace Hyperframework\Cli;

class OptionCallback {
    public static function dispatch($options, $config) {
        while ($this->dispatch([
            '-x' => function($ctx) use ($options) {
                $ctx->getOptions();
                $this->dispatch([
                    '-y' => function($value) {
                        $this->stopDispatch(2);
                        return;
                    },
                    '-x' => function($value) {
                    }
                ]);
                $ctx->getOptionValue();
                $ctx->quit();
                return;
            }
        ], [
            '-y' => function() {
            }
        ]));

        OptionRouter::run(['-x' => function() {
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
