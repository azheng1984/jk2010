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

    public static function compare($levelA, $levelB, $operator = null) {
        if (is_int($levelA) === false) {
            $levelA = static::getCode($levelA);
            if ($levelA === null) {
                throw new InvalidArgumentException(
                    "Argument 'levelA' is invalid."
                );
            }
        } elseif ($levelA < -1 || $levelA > 5) {
            throw new InvalidArgumentException(
                "Argument 'levelA' is invalid."
            );
        }
        if (is_int($levelB) === false) {
            $levelB = static::getCode($levelB);
            if ($levelB === null) {
                throw new InvalidArgumentException(
                    "Argument 'levelB' is invalid."
                );
            }
        } elseif ($levelB < -1 || $levelB > 5) {
            throw new InvalidArgumentException(
                "Argument 'levelB' is invalid."
            );
        }
        if ($operator === null) {
            if ($levelA === $levelB) {
                return 0;
            }
            if ($levelA > $levelB) {
                return 1;
            }
            return -1;
        }
        switch ($operator) {
            case '>':
                return $levelA > $levelB;
            case '>=':
                return $levelA >= $levelB;
            case '<':
                return $levelA < $levelB;
            case '<=':
                return $levelA <= $levelB;
            case '==':
                return $levelA === $levelB;
            case '!=':
                return $levelA !== $levelB;
            default:
                throw new InvalidArgumentException(
                    "Argument 'operator' is invalid."
                );
        }
    }
}
