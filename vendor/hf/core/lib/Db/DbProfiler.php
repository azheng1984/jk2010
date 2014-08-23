<?php
namespace Hyperframework\Db;

class DbProfiler {
    private static $current;
    private static $profiles = array();

    public static function onConnectionExecuting($connection, $sql, $isQuery) {
        self::$current = array(
            'start_time' => microtime(true),
            'sql' => $sql
        );
    }

    public static function onConnectionExecuted($connection, $result) {
        self::$profiles[] = array(
            'connection_name' => $connection->getName(),
            'sql' => self::$current['sql'],
            'time' => self::$current['start_time'] - microtime(true)
        );
    }

    public static function onStatementExecuting($statement) {
        self::$current = array('start_time' => microtime(true));
    }

    public static function onStatementExecuted($statement) {
        self::$profiles[] = array(
            'connection_name' => $statement->getConnection()->getName(),
            'sql' => $statement->getSql(),
            'time' => self::$current['start_time'] - microtime(true)
        );
    }

    public static function getProfiles() {
        return self::$profiles;
    }
}
