<?php
namespace Hyperframework\Logging;

use InvalidArgumentException;

class LogLevelHelper {
    private static $levels = [
        'OFF' => -1,
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

    public static function compare($level1, $level2, $operator = null) {
        if (is_int($level1) === false) {
            $level1 = static::getCode($level1);
            if ($level1 === null) {
                throw new InvalidArgumentException(
                    "Argument 'level1' is invalid."
                );
            }
        } elseif ($level1 < -1 || $level1 > 5) {
            throw new InvalidArgumentException(
                "Argument 'level1' is invalid."
            );
        }
        if (is_int($level2) === false) {
            $level2 = static::getCode($level2);
            if ($level2 === null) {
                throw new InvalidArgumentException(
                    "Argument 'level2' is invalid."
                );
            }
        } elseif ($level2 < -1 || $level2 > 5) {
            throw new InvalidArgumentException(
                "Argument 'level2' is invalid."
            );
        }
        if ($operator === null) {
            if ($level1 === $level2) {
                return 0;
            }
            if ($level1 > $level2) {
                return 1;
            }
            return -1;
        }
        switch ($operator) {
            case '>':
                return $level1 > $level2;
            case '>=':
                return $level1 >= $level2;
            case '<':
                return $level1 < $level2;
            case '<=':
                return $level1 <= $level2;
            case '==':
                return $level1 === $level2;
            case '!=':
                return $level1 !== $level2;
            default:
                throw new InvalidArgumentException(
                    "Argument 'operator' is invalid."
                );
        }
    }
}
