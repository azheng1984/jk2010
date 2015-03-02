<?php
namespace Hyperframework\Common;

class ExitHelper {
    public static function exitScript($status = 0) {
        $exitFunction = Config::get('hyperframework.exit_function');
        if ($exitFunction === null) {
            exit($status);
        } elseif (is_callable($exitFunction) === false) {
            throw new ConfigException(
                "Exit function is not callable, set using config "
                    . "'hyperframework.exit_function'."
            );
        }
        call_user_func($exitFunction, $status);
    }
}
