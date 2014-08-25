<?php
namespace Hyperframework;

class Logger {
    private static $levels = array(
        'emergency' => 0,
        'alert' => 1,
        'critical' => 2,
        'error' => 3,
        'warn' => 4,
        'notice' => 5,
        'info' => 6,
        'debug' => 7,
    );

    public static function debug($message, $context = null) {
        static::write('debug',$message, $context);
    }

    public static function info($message, $context = null) {
        static::write('info',$message, $context);
    }

    public static function notice($message, $context = null) {
        static::write('notice', $entry);
    }

    public static function warn($entry) {
        static::output('warn', $entry);
    }

    public static function error($entry) {
        static::output('error', $entry);
    }

    public static function critical($entry) {
        static::write('critical', $entry);
    }

    public static function alert() {
    }

    public static function emergancy() {
    }

    protected static function write($level, $message, $context) {
        $level = Config::get('hyperframework.log_level');
        if ($level === null) {
            $level = 'warn';
        }
        if ($type < self::$types[$level]) {
            return;
        }
        $writer = Config::get('hyperframework.log_writer');
        if ($writer !== null) {
            $writer::write($type, $entry);
            return;
        }
        $path = Config::get('hyperframework.log_path');
        if ($path === null) {
            $path = APP_ROOT_PATH . 'data' . DIRECTORY_SEPARATOR . 'log.txt';
        } elseif (FullPathRecognizer::isFull($path) === false) {
            $path = APP_ROOT_PATH . DIRECTORY_SEPARATOR . $path;
        }
        file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
    }
}
