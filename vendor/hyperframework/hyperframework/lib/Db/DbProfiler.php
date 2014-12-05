<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Logging\Logger;

class DbProfiler {
    private static $current;
    private static $profileHandlers = array();

    public static function onTransactionOperationExecuting(
        $connection, $operation
    ) {
        self::$current = array(
            'transaction' => $operation, 'start_time' => microtime(true)
        );
        //todo move connection name field to to top
        $connectionName = $connection->getName();
        if ($connectionName !== 'default') {
            self::$current['connection_name'] = $connectionName;
        }
    }

    public static function onTransactionOperationExecuted() {
        self::$current['running_time'] = self::getRunningTime();
        self::handleProfile(self::$current);
    }

    public static function onConnectionExecuting(
        $connection, $sql, $isQuery
    ) {
        self::$current = array(
            'sql' => $sql, 'start_time' => microtime(true)
        );
        $connectionName = $connection->getName();
        if ($connectionName !== 'default') {
            self::$current['connection_name'] = $connectionName;
        }
    }

    public static function onConnectionExecuted(
        $connection, $result
    ) {
        self::$current['running_time'] = self::getRunningTime();
        self::handleProfile(self::$current);
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
        self::handleProfile($profile);
    }

    public static function addProfileHandler($callback) {
        self::$profileHandler[] = $callback;
    }

    private static function handleProfile($profile) {
        $isLoggerEnabled = Config::get(
            'hyperframework.db.profiler.enable_logger'
        );
        if ($isLoggerEnabled === null) {
            $isLoggerEnabled = true;
        }
        if ($isLoggerEnabled != false) {
            Logger::debug('hyperframework.db.profiler.profile', $profile);
        }
        foreach (self::$profileHandlers as $handler) {
            call_user_func($handler, $profile);
        }
    }

    private static function getRunningTime() {
        return sprintf('%F', microtime(true) - self::$current['start_time']);
    }
}
