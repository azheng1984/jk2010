<?php
namespace Hyperframework\Logging;

use InvalidArgumentException;

class LogLevelHelper {
    private static $levels = [
        'FATAL' => 0,
        'ERROR' => 1,
        'WARNING' => 2,
        'NOTICE' => 3,
        'INFO' => 4,
        'DEBUG' => 5
    ];

    public static function getCode($name) {
        if (isset(self::$levels[$name]) === false) {
            $name = strtoupper($name);
            if (isset(self::$levels[$name]) === false) {
                return;
            }
        }
        return self::$levels[$name];
    }

    public static function getName($code) {
        $name = array_search($code, self::$levels, true);
        if ($name === false) {
            return null;
        }
        return $name;
    }
}
