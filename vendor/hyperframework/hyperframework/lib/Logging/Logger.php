<?php
namespace Hyperframework\Logging;

use Closure;
use DateTime;
use InvalidArgumentException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigException;
use Hyperframework\Common\ClassNotFoundException;

final class Logger {
    private static $logHandler;
    private static $thresholdCode;
    private static $levels = [
        'OFF' => -1,
        'FATAL' => 0,
        'ERROR' => 1,
        'WARNING' => 2,
        'NOTICE' => 3,
        'INFO' => 4,
        'DEBUG' => 5
    ];

    public static function debug($mixed) {
        if (self::getThresholdCode() === 5) {
            static::log('DEBUG', $mixed);
        }
    }

    public static function info($mixed) {
        if (self::getThresholdCode() >= 4) {
            static::log('INFO', $mixed);
        }
    }

    public static function notice($mixed) {
        if (self::getThresholdCode() >= 3) {
            static::log('NOTICE', $mixed);
        }
    }

    public static function warn($mixed) {
        if (self::getThresholdCode() >= 2) {
            static::log('WARNING', $mixed);
        }
    }

    public static function error($mixed) {
        if (self::getThresholdCode() >= 1) {
           static::log('ERROR', $mixed);
        }
    }

    public static function fatal($mixed) {
        if (self::getThresholdCode() >= 0) {
            static::log('FATAL', $mixed);
        }
    }

    public static function setLevel($value) {
        if ($value === null) {
            self::$thresholdCode = null;
            return;
        }
        if (isset(self::$levels[$value]) === false) {
            $tmp = strtoupper($value);
            if (isset(self::$levels[$tmp]) === false) {
                throw new InvalidArgumentException(
                    "Log level '$value' is invalid."
                );
            }
            $value = $tmp;
        }
        self::$thresholdCode = self::$levels[$value];
    }

    public static function getLevel() {
        return array_search(self::getThresholdCode(), self::$levels);
    }

    public static function setLogHandler($value) {
        self::$logHandler = $value;
    }

    public static function getLogHandler() {
        if (self::$logHandler === null) {
            $class = Config::getString(
                'hyperframework.logging.log_handler_class', ''
            );
            if ($class === '') {
                self::$logHandler = new LogHandler;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Log handler class '$class' does not exist, defined in "
                            . "'hyperframework.logging.log_handler_class'."
                    );
                }
                self::$logHandler = new $class;
            }
        }
        return self::$logHandler;
    }

    private static function log($level, $options) {
        if ($options instanceof Closure) {
            $options = $options();
        }
        if (is_string($options)) {
            $options = ['message' => $options];
        } elseif (is_array($options) === false) {
            throw new LoggingException(
                'Invalid log entry, ' . gettype($options) . ' given.'
            );
        }
        $options['level'] = $level;
        if (isset($options['time']) !== false
            && is_int($options['time']) === false
            && $options['time'] instanceof DateTime === false
        ) {
            $type = gettype($options['time']);
            if ($type === 'object') {
                $type = get_class($options['time']);
            }
            throw new LoggingException(
                "Log entry time must be a DateTime or an integer, "
                    . $type . " given."
            );
        }
        if (isset($options['name'])) {
            if (preg_match('/^[a-zA-Z0-9_.]+$/', $options['name']) === 0
                || $options['name'][0] === '.'
                || substr($options['name'], -1) === '.'
            ) {
                throw new LoggingException(
                    "Log entry name '{$options['name']}' is invalid."
                );
            }
        }
        if (isset($options['message'])) {
            if (is_array($options['message'])) {
                $count = count($options['message']);
                if ($count === 0) {
                    $options['message'] = '';
                } elseif ($count === 1) {
                    $options['message'] = $options['message'][0];
                } else {
                    $options['message'] =
                        call_user_func_array('sprintf', $options['message']);
                }
            }
        }
        if (isset($options['data'])) {
            if (is_array($options['data']) === false) {
                throw new LoggingException(
                    "Log entry option 'data' must be an array, "
                        . gettype($options['data']) . ' given.'
                );
            }
            self::checkDataKey($options['data']);
        }
        $logHandler = self::getLogHandler();
        $logHandler->handle($level, $options);
    }

    private static function checkDataKey(array $data) {
        foreach ($data as $key => $value) {
            if (preg_match('/^[0-9a-zA-Z_]+$/', $key) === 0) {
                throw new LoggingException(
                    "Log entry option 'data' is invalid,"
                        . " key '$key' is not allowed."
                );
            }
            if (is_array($value)) {
                self::checkDataKey($value);
            }
        }
    }

    private static function getThresholdCode() {
        if (self::$thresholdCode === null) {
            $level = Config::getString('hyperframework.logging.log_level', '');
            if ($level !== '') {
                if (isset(self::$levels[$level]) === false) {
                    $tmp = strtoupper($level);
                    if (isset(self::$levels[$tmp]) === false) {
                        throw new ConfigException(
                            "Log level '$level' is invalid, defined in "
                                . "'hyperframework.logging.log_level'."
                        );
                    }
                    $level = $tmp;
                }
                self::$thresholdCode = self::$levels[$level];
            } else {
                self::$thresholdCode = 4;
            }
        }
        return self::$thresholdCode;
    }
}
