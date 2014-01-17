<?php
namespace Hyperframework;

class Logger {
    const TYPE_TRACE = 2;
    const TYPE_DEBUG = 4;
    const TYPE_INFO  = 8;
    const TYPE_WARN  = 16;
    const TYPE_ERROR = 32;

    public static function trace($entry) {
        static::output(self::TYPE_TRACE, $entry);
    }

    public static function debug($entry) {
        static::output(self::TYPE_DEBUG, $entry);
    }

    public static function info($entry) {
        static::output(self::TYPE_INFO, $entry);
    }

    public static function warn($entry) {
        static::output(self::TYPE_WARN, $entry);
    }

    public static function error($entry) {
        static::output(self::TYPE_ERROR, $entry);
    }

    protected static function output($type, $entry) {
        $errorReporting = Config::get(
            __CLASS__ . '\ErrorReporting',
            //todo 使用常量会导致 logger 类被提前加载
            array('default' => self::TYPE_WARN | self::TYPE_ERROR)
        );
        if ($type & $errorReporting === 0) {
            return;
        }
        $delegate = Config::get(__CLASS__, '\Delegate');
        if ($delegate !== null) {
            $delegate->output($type, $entry);
            return;
        }
        $path = Config::get(
            __CLASS__ . '\LogPath',
            array('default' => array('relative_path' => 'data/log.txt'))
        );
        file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
    }
}
