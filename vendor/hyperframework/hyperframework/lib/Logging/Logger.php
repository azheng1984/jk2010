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

    public static function debug($params) {
        if (self::getThresholdCode() === 5) {
            static::log('DEBUG', $params);
        }
    }

    public static function info($params) {
        if (self::getThresholdCode() >= 4) {
            static::log('INFO', $params);
        }
    }

    public static function notice($params) {
        if (self::getThresholdCode() >= 3) {
            static::log('NOTICE', $params);
        }
    }

    public static function warn($params) {
        if (self::getThresholdCode() >= 2) {
            static::log('WARNING', $params);
        }
    }

    public static function error($params) {
        if (self::getThresholdCode() >= 1) {
           static::log('ERROR', $params);
        }
    }

    public static function fatal($params) {
        if (self::getThresholdCode() >= 0) {
            static::log('FATAL', $params);
        }
    }

    private static function log($level, $params) {
        if (isset($params['name'])) {
            if (preg_match('/^[a-zA-Z0-9_.]+$/', $params['name']) === 0
                || $params['name'][0] === '.'
                || substr($params['name'], -1) === '.'
            ) {
                throw new Exception;
            }
        }
        if ($params instanceof Closure) {
            $params = $params();
        }
        if (isset($params['message'])) {
            if (is_array($params['message'])) {
                if (count($params['message']) < 2) {
                    throw new Exception;
                } else {
                    $opitons['message'] =
                        call_user_func_array('sprintf', $opitons['message']);
                }
            }
        }
        if (isset($params['data'])) {
            if (is_array($params['data']) === false) {
                throw new Exception;
            }
        }
        $handler = Config::get('hyperframework.logger.log_handler');
        if ($handler == null) {
            LogHandler::handle($level, $params);
        } else {
            $handler::handle($level, $params);
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
