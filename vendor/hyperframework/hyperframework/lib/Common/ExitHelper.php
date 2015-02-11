<?php
namespace Hyperframework\Common;

class ExitHelper {
    public static function exitScript($status = 0) {
        $exitFunction = Config::getString('hyperframework.exit_function');
        if ($exitFunction === null) {
            exit($status);
        } elseif (is_callable($exitFunction) === false) {
            throw new ConfigException(
                "Exit function is not callable, defined in config '"
                    . "hyperframework.exit_function'."
            );
        }
        call_user_func($exitFunction, $status);
    }
}
