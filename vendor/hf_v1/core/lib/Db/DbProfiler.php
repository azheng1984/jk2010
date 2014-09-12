<?php
namespace Hyperframework\Db;

use Hyperframework\Logger;

class DbProfiler {
    private static $current;
    private static $profiles = array();

    public static function onConnectionExecuting($connection, $sql, $isQuery) {
        self::$current = array(
            'connection_name' => $connection->getName(),
            'sql' => $sql,
            'start_time' => microtime(true)
        );
    }

    public static function onConnectionExecuted($connection, $result) {
        self::$current['running_time'] = self::getRunningTime();
        self::$profiles[] = self::$current;
        Logger::debug('hyperframework.db.profiler.profile', self::$current);
    }

    public static function onStatementExecuting($statement) {
        self::$current = array('start_time' => microtime(true));
    }

    public static function onStatementExecuted($statement) {
        $profile = array(
            'connection_name' => $statement->getConnection()->getName(),
            'sql' => $statement->getSql(),
            'start_time' => self::$current['start_time'],
            'running_time' => self::getRunningTime()
        );
        self::$profiles[] = $profile;
        Logger::debug('hyperframework.db.profiler.profile', $profile);
    }

    public static function getProfiles() {
        return self::$profiles;
    }

    private static function getRunningTime() {
        return microtime(true) - self::$current['start_time'];
    }
}
