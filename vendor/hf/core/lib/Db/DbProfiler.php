<?php
namespace Hyperframework\Db;

class DbProfiler {
    private static $startTime;

    public static function onConnectionExecuting($connection, $sql, $isQuery) {
        echo $sql . '<br>';
        self::$startTime = microtime(true);
    }

    public static function onConnectionExecuted($connection, $result) {
        $time_end = microtime(true);
        $time = $time_end - self::$startTime;
        echo $time * 1000 . 'ms' . '<br>';
    }

    public static function onStatementExecuting($statement) {
        echo $statement->getSql() . '<br>';
        self::$startTime = microtime(true);
    }

    public static function onStatementExecuted($statement) {
        $time_end = microtime(true);
        $time = $time_end - self::$startTime;
        echo $time * 1000 . 'ms' . '<br>';
    }

    public static function getProfile($param) {
        return null;
    }
}
