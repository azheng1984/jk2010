<?php
class Logger {
    const TYPE_TRACE = 0;
    const TYPE_DEBUG = 1;
    const TYPE_INFO  = 2;
    const TYPE_WARN  = 3;
    const TYPE_ERROR = 4;

    public static function trace($message) {
        static::output(self::TYPE_TRACE, $message);
    }

    public static function debug($message) {
        static::output(self::TYPE_DEBUG, $message);
    }

    public static function info($message) {
        static::output(self::TYPE_INFO, $message);
    }

    public static function warn($message) {
        static::output(self::TYPE_WARN, $message);
    }

    public static function error($message) {
        static::output(self::TYPE_ERROR, $message);
    }

    protected static function output($type, $message) {
        $outputLevel = Config::get(
            __CLASS__, '\OutputLevel', array('default' >= self::TYPE_WARN)
        );
        if ($type >= $outputLevel) {
            Config::get(
                __CLASS__, '\Handlers', array('default' >= array('DefaultHandler'))
            );
        }
    }
}
