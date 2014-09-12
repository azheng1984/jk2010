<?php
namespace Hyperframework\Db;

use Hyperframework\Config;
use Hyperframework\Logger;

class DbProfiler {
    private static $current;

    public static function onConnectionExecuting($connection, $sql, $isQuery) {
        self::$current = array(
            'sql' => $sql,
            'start_time' => microtime(true)
        );
        $connectionName = $connection->getName();
        if ($connectionName !== 'default') {
            self::$current['connection_name'] = $connectionName;
        }
    }

    public static function onConnectionExecuted($connection, $result) {
        self::$current['running_time'] = self::getRunningTime();
        self::handle(self::$current);
    }

    public static function onStatementExecuting($statement) {
        self::$current = array('start_time' => microtime(true));
    }

    public static function onStatementExecuted($statement) {
        $profile = array(
            'sql' => $statement->getSql(),
            'start_time' => self::$current['start_time'],
            'running_time' => self::getRunningTime()
        );
        $connectionName = $statement->getConnection()->getName();
        if ($connectionName !== 'default') {
            $profile['connection_name'] = $connectionName;
        }
        self::handle($profile);
    }

    private static function handle($profile) {
        $callback = Config::get(
            'hyperframework.db.profiler.handle_profile_callback'
        );
        if ($callback === null) {
            Logger::debug('hyperframework.db.profiler.profile', $profile);
        } else {
            call_user_func($callback, $profile);
        }
    }

    private static function getRunningTime() {
        return microtime(true) - self::$current['start_time'];
    }
}
