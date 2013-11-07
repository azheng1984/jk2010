<?php
namespace Hyperframework;

class EnvConfig {
    private static $data = array();

    public static function has($pathConfigName) {
        if (isset(static::$data[$pathConfigName])) {
            return static::$data[$pathConfigName];
        }
    }

    public static function enable($pathConfigName) {
        return static::$data[$pathConfigName] = true;
    }

    public static function disable($pathConfigName) {
        return static::$data[$pathConfigName] = false;
    }

    public static function export() {
        return static::$data;
    }

    public static function reset() {
        static::$data = array();
    }
}

EnvConfig::enable('\Hyperframework\Db\DbConnectionFactory\ConfigPath');
