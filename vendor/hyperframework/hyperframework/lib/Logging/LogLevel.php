<?php
namespace Hyperframework\Logging;

class LogLevel {
    const FATAL   = 0;
    const ERROR   = 1;
    const WARNING = 2;
    const NOTICE  = 3;
    const INFO    = 4;
    const DEBUG   = 5;

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
