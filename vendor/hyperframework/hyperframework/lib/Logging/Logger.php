<?php
namespace Hyperframework\Logging;

use Exception;
use Hyperframework\Common\Config;

final class Logger {
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

    public static function debug(/*...*/) {
        if (self::getThresholdCode() === 5) {
            static::log('DEBUG', func_get_args());
        }
    }

    public static function info(/*...*/) {
        if (self::getThresholdCode() >= 4) {
            static::log('INFO', func_get_args());
        }
    }

    public static function notice(/*...*/) {
        if (self::getThresholdCode() >= 3) {
            static::log('NOTICE', func_get_args());
        }
    }

    public static function warn(/*...*/) {
        if (self::getThresholdCode() >= 2) {
            static::log('WARNING', func_get_args());
        }
    }

    public static function error(/*...*/) {
        if (self::getThresholdCode() >= 1) {
            static::log('ERROR', func_get_args());
        }
    }

    public static function fatal(/*...*/) {
        if (self::getThresholdCode() >= 0) {
            static::log('FATAL', func_get_args());
        }
    }

    private static function log($level, array $args) {
        $handler = Config::get('hyperframework.logger.log_handler');
        if ($handler == null) {
            LogHandler::handle($level, $args);
        } else {
            $handler::handle($level, $args);
        }
    }

    private static function getThresholdCode() {
        if (self::$thresholdCode === null) {
            $level = Config::get('hyperframework.logger.log_level');
            if ($level !== null) {
                if (isset(self::$levels[$level]) === false) {
                    throw new Exception;
                }
                self::$thresholdCode = self::$levels[$level];
            } else {
                self::$thresholdCode = 4;
            }
        }
        return self::$thresholdCode;
    }
}
