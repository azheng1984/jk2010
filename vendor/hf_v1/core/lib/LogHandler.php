<?php
namespace Hyperframework;

use Exception;
use Closure;

class LogHandler {
    private static $path;
    public static function log($level, array $params) {
//      Config::('hyperframework.log_handler.syslog.enable');
//      Config::('hyperframework.log_handler.file.enable');
//      Config::('hyperframework.log_handler.file.path');
//      Config::('hyperframework.log_handler.file.paths');
        $content = static::format($level, $params);
        static::writeFile($content);
    }

    protected static function handleFile() {
    }

    protected static function handleSyslog() {
    }

    public static function writeSyslog($level, $content) {
        $priority = null;
        switch ($level) {
            case 'DEBUG': $priority = LOG_DEBUG; break;
            case 'INFO': $priority = LOG_INFO; break;
            case 'NOTICE': $priority = LOG_NOTICE; break;
            case 'WARNING': $priority = LOG_WARNING; break;
            default: $priority = LOG_ERR;
        }
        syslog($priority, $content);
    }

    protected static function writeFile($content) {
        $path = static::getDefaultFilePath();
        if (file_put_contents($path, $content, FILE_APPEND | LOCK_EX) === false)
        {
            throw new Exception;
        }
    }

    protected static function getDefaultFilePath() {
        if (self::$path === null) {
            $path = Config::get('hyperframework.logger.path');
            if ($path === null) {
                $path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'log'
                    . DIRECTORY_SEPARATOR . 'app.log';
            } elseif (FullPathRecognizer::isFull($path) === false) {
                $path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . $path;
            }
            self::$path = $path;
        }
        return self::$path;
    }

    protected static function format($level, array $params) {
        $count = count($params);
        if ($count > 0 && $params[0] instanceof Closure) {
            if ($count > 1) {
                throw new Exception;
            }
            $callback = $params[0];
            $params = $callback();
            if (is_array($params)) {
                $count = count($params);
            } else {
                $params = array($params);
                $count = 1;
            }
        }
        $prefix = PHP_EOL . '[' . date('Y-m-d h:i:s') . '] [' . $level . '] ';
        $message = null;
        if ($count > 0) {
            if ($count > 1) {
                $message = call_user_func_array('sprintf', $params);
            } else {
                $message = $params[0];
            }
        }
        return $prefix . str_replace(PHP_EOL, PHP_EOL . "\t", $message);
    }
}
