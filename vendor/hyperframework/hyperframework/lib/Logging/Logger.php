<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;

final class Logger {
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
        if ($params instanceof Closure) {
            $params = $params();
        }
        if (isset($params['message'])) {
            if (is_array($params['message'])) {
                $count = count($params['message']);
                if ($count === 0) {
                    unset($params['message']);
                } elseif ($count === 1) {
                    $params['message'] = $params['message'][0];
                } else {
                    $params['message'] =
                        call_user_func_array('sprintf', $opitons['message']);
                }
            }
        }
        if (isset($params['data'])) {
            if (is_array($params['data']) === false) {
                throw new LoggingException('Data of log entry must be array, '
                    . gettype($params['data']) . ' given.');
            }
        }
        $logHandlerClass = Config::getString(
            'hyperframework.logger.log_handler_class', ''
        );
        if ($logHandlerClass === '') {
            LogHandler::handle($level, $params);
        } else {
            if (class_exists($logHandlerClass) === false) {
                throw new Loggingxception(
                    "Log handler class '$class' do not exist."
                );
            }
            $logHandlerClass::handle($level, $params);
        }
    }

    private static function getThresholdCode() {
        if (self::$thresholdCode === null) {
            $level = Config::getString('hyperframework.logger.log_level', '');
            if ($level !== '') {
                if (isset(self::$levels[$level]) === false) {
                    $level = strtoupper($level);
                    if (isset(self::$levels[$level]) === false) {
                        throw new LoggingException(
                            "Log entry level '$level' is invalid."
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
