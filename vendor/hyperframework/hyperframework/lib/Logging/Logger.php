<?php
namespace Hyperframework\Logging;

use Closure;
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
        if (is_string($params)) {
            $params = ['message' => $params];
        }
        if ($params instanceof Closure) {
            $params = $params();
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
