<?php
namespace Hyperframework\Logging;

use Exception;
use Closure;

class Logger {
    private static $thresholdCode;
    private static $path;
    private static $levels = array(
        'fatal' => 0,
        'error' => 1,
        'warning' => 2,
        'notice' => 3,
        'info' => 4,
        'debug' => 5
    );

    public static function debug(/*$param, ...*/) {
        if (self::getThresholdCode() === 5) {
            static::log('debug', func_get_args());
        }
    }

    public static function info(/*$param, ...*/) {
        if (self::getThresholdCode() >= 4) {
            static::log('info', func_get_args());
        }
    }

    public static function notice(/*$param, ...*/) {
        if (self::getThresholdCode() >= 3) {
            static::log('notice', func_get_args());
        }
    }

    public static function warn(/*$param, ...*/) {
        if (self::getThresholdCode() >= 2) {
            static::log('warning', func_get_args());
        }
    }

    public static function error(/*$param, ...*/) {
        if (self::getThresholdCode() >= 1) {
            static::log('error', func_get_args());
        }
    }

    public static function fatal(/*$param, ...*/) {
        if (self::getThresholdCode() >= 0) {
            static::log('fatal', func_get_args());
        }
    }

    private static function log($level, array $params) {
        $content = null;
        $handler = Config::get('hyperframework.logger.handler');
        if ($handler == null) {
            $content = StringFormatter::format($level, $params);
            FileWriter::write($content);
        }
        $handler::log($level, $params);
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
