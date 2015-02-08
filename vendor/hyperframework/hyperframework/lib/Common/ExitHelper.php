<?php
namespace Hyperframework\Common;

class ExitHelper {
    public function exitScript($status = 0) {
        $exitFunction = Config::getString('hyperframework.exit_function', '');
        if ($exitFunction === '') {
            exit($status);
        } elseif (is_callable($exitFunction) === false) {
            throw new ConfigException(
                "Exit function is not callable, defined in '"
                    . "hyperframework.exit_function'."
            );
        }
        $exitFunction($status);
    }
}
