<?php
namespace Hyperframework;

class Logger {
    private static $thresholdCode;
    private static $path;
    private static $levels = array(
        'FATAL' => 0,
        'ERROR' => 1,
        'WARNING' => 2,
        'NOTICE' => 3,
        'INFO' => 4,
        'DEBUG' => 5
    );

    public static function debug(/*$param, ...*/) {
        if (self::getThresholdCode() === 5) {
            static::log('DEBUG', func_get_args());
        }
    }

    public static function info(/*$param, ...*/) {
        if (self::getThresholdCode() >= 4) {
            static::log('INFO', func_get_args());
        }
    }

    public static function notice(/*$param, ...*/) {
        if (self::getThresholdCode() >= 3) {
            static::log('NOTICE', func_get_args());
        }
    }

    public static function warn(/*$param, ...*/) {
        if (self::getThresholdCode() >= 2) {
            static::log('WARNING', func_get_args());
        }
    }

    public static function error(/*$param, ...*/) {
        if (self::getThresholdCode() >= 1) {
            static::log('ERROR', func_get_args());
        }
    }

    public static function fatal(/*$param, ...*/) {
        if (self::getThresholdCode() >= 0) {
            static::log('FATAL', func_get_args());
        }
    }

    private static function log($level, array $params) {
        $handler = Config::get('hyperframework.logger.handler');
        if ($handler == null) {
            LogHandler::log($level, $params);
        } else {
            $handler::log($level, $params);
        }
    }

    private static function getThresholdCode() {
        if (self::$thresholdCode === null) {
            $level = Config::get('hyperframework.logger.level');
            if ($level !== null && isset(self::$levels[$level])) {
                self::$thresholdCode = self::$levels[$level];
            } else {
                self::$thresholdCode = 4;
            }
        }
        return self::$thresholdCode;
    }
}
