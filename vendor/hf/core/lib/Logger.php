<?php
namespace Hyperframework;

class Logger {
    private static $types = array(
        'trace' => 0,
        'debug' => 1,
        'info' => 2,
        'warn' => 3,
        'error' => 4,
    );

    public static function trace($entry) {
        static::output('trace', $entry);
    }

    public static function debug($entry) {
        static::output('debug', $entry);
    }

    public static function info($entry) {
        static::output('info', $entry);
    }

    public static function warn($entry) {
        static::output('warn', $entry);
    }

    public static function error($entry) {
        static::output('error', $entry);
    }

    protected static function output($type, $entry) {
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
            $path = APPLICATION_PATH . 'data' . DIRECTORY_SEPARATOR . 'log.txt';
        } elseif (FullPathRecognizer::isFull($path) === false) {
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $path;
        }
        file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
    }
}
