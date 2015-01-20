<?php
namespace Hyperframework\Logging;

use Closure;
use DateTime;
use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigException;
use Hyperframework\Common\ClassNotFoundException;

final class Logger {
    private static $logHandler;
    private static $thresholdCode;
    private static $path;
    private static $levels = [
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

    private static function log($level, $params) {
        if ($params instanceof Closure) {
            $params = $params();
        }
        if (is_string($params)) {
            $params = ['message' => $params];
        } elseif (is_array($params) === false) {
            throw new LoggingException(
                'Invalid log entry, ' . gettype($params) . ' given.'
            );
        }
        if (isset($params['time']) !== false
            && is_int($params['time']) === false
            && $params['time'] instanceof DateTime === false
        ) {
            throw new LoggingException(
                "Log entry field 'time' must be an integer or DateTime, "
                    . gettype($params['time']) . " given."
            );
        }
        if (isset($params['name'])) {
            if (preg_match('/^[a-zA-Z0-9_.]+$/', $params['name']) === 0
                || $params['name'][0] === '.'
                || substr($params['name'], -1) === '.'
            ) {
                throw new LoggingException(
                    "Log entry name '{$params['name']}' is invalid."
                );
            }
        }
        if (isset($params['message'])) {
            if (is_array($params['message'])) {
                $count = count($params['message']);
                if ($count === 0) {
                    $params['message'] = '';
                } elseif ($count === 1) {
                    $params['message'] = $params['message'][0];
                } else {
                    $params['message'] =
                        call_user_func_array('sprintf', $params['message']);
                }
            }
        }
        if (isset($params['data'])) {
            if (is_array($params['data']) === false) {
                throw new LoggingException(
                    "Log entry field 'data' must be an array, "
                        . gettype($params['data']) . ' given.'
                );
            }
            foreach ($params['data'] as $key => $value) {
                if (preg_match('/^[0-9a-zA-Z_]+$/', $key) === 0) {
                    throw new LoggingException(
                        "Log entry feild 'data' is invalid,"
                            . " key '$key' is not allowed."
                    );
                }
            }
        }
        $logHandler = self::getLogHandler();
        $logHandler->handle($level, $params);
    }

    private static function getLogHandler() {
        if (self::$logHandler === null) {
            $class = Config::getString(
                'hyperframework.logger.log_handler_class', ''
            );
            if ($class === '') {
                $class = 'Hyperframework\Logging\LogHandler';
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Log handler class '$class' does not exist, defined in "
                            . "'hyperframework.logger.log_handler_class'."
                    );
                }
            }
            self::$logHandler = new $class;
        }
        return self::$logHandler;
    }

    private static function getThresholdCode() {
        if (self::$thresholdCode === null) {
            $level = Config::getString('hyperframework.logger.log_level', '');
            if ($level !== '') {
                if (isset(self::$levels[$level]) === false) {
                    $level = strtoupper($level);
                    if (isset(self::$levels[$level]) === false) {
                        throw new ConfigException(
                            "Log entry level '$level' is invalid, defined in "
                                . "'hyperframework.logger.log_level'."
                        );
                    }
                }
                self::$thresholdCode = self::$levels[$level];
            } else {
                self::$thresholdCode = 4;
            }
        }
        return self::$thresholdCode;
    }
}
