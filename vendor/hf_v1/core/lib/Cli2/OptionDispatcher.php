<?php
namespace Hyperframework\Cli;

class OptionGroupDispatcher {
    private static $depth = 0;
    private static $stoppedDepths = array();

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

    public static function dispatch($options, $config) {
        ++self::$depth;
        if (isset(self::$stoppedDepths[self::$depth])) {
            throw new Exception;
        }
        --self::$depth;
    }

    public static function dispatchAll($options, $config) {
        ++self::$depth;
        if (isset(self::$stoppedDepths[self::$depth])) {
            //todo break
            unset(self::$stoppedDepths[self::$depth]);
        }
        --self::$depth;
    }

    public static function stopDispatch() {
        //if not dispatch all throw exception
        if (self::$depth === 0) {
            throw new Exception;
        }
        self::$stoppedDepths[self::$depth] = true;
    }
}
