<?php
namespace Hyperframework;

use Exception;

class LogHandler {
    public static function log($level, array $params) {
//      Config::('hyperframework.log_handler.syslog.enable');
//      Config::('hyperframework.log_handler.file.enable');
//      Config::('hyperframework.log_handler.file.path');
//      Config::('hyperframework.log_handler.file.paths');
        static::format($level, $params);
    }

    protected static function handleFile() {
    }

    protected static function handleSyslog() {
    }

    public static function writeSyslog($level, $content) {
        $priority = null;
        switch ($level) {
            case 'debug': $priority = LOG_DEBUG; break;
            case 'info': $priority = LOG_INFO; break;
            case 'notice': $priority = LOG_NOTICE; break;
            case 'warning': $priority = LOG_WARNING; break;
            default: $priority = LOG_ERR;
        }
        syslog($priority, $content);
    }

    protected static function writeFile($content) {
        $path = static::getPath();
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
        if ($count === 0) {
            return;
        }
        if ($params[0] instanceof Closure) {
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
        $prefix = PHP_EOL . date('Y-m-d h:i:s') . ' [' . $level . '] ';
        if ($count > 1) {
            return $prefix . call_user_func_array('sprintf', $params);
        } else {
            return $prefix . $params[0];
        }
    }
}
