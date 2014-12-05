<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Logging\Logger;

class DbProfiler {
    private static $profile;
    private static $profileHandlers = array();

    public static function onTransactionOperationExecuting(
        $connection, $operation
    ) {
        self::initializeProfile($connection, ['transaction' => $operation]);
    }

    public static function onTransactionOperationExecuted(
        $connection, $operation
    ) {
        self::handleProfile();
    }

    public static function onConnectionExecuting($connection, $sql, $isQuery) {
        self::initializeProfile($connection, ['sql' => $sql]);
    }

    public static function onConnectionExecuted(
        $connection, $result
    ) {
        self::handleProfile();
    }

    public static function onStatementExecuting($statement) {
        self::initializeProfile(
            $statement->getConnection(),
            ['sql' => $statement->getsql()]
        );
    }

    public static function onStatementExecuted($statement) {
        self::handleProfile();
    }

    private static function initializeProfile($connection, $data) {
        self::$profile = [];
        $name = $connection->getName();
        if ($name !== 'default') {
            self::$profile['connection_name'] => $name;
        }
        self::$profile = array_merge(self::$profile, $data);
        self::$profile['start_time'] => microtime(true);
    }

    public static function addProfileHandler($callback) {
        self::$profileHandler[] = $callback;
    }

    private static function handleProfile() {
        self::$profile['running_time'] = sprintf(
            '%F', microtime(true) - self::$profile['start_time']
        );
        $isLoggerEnabled = Config::get(
            'hyperframework.db.profiler.enable_logger'
        );
        if ($isLoggerEnabled === null) {
            $isLoggerEnabled = true;
        }
        if ($isLoggerEnabled) {
            Logger::debug(
                'name' => 'hyperframework.db.profiler.profile',
                'data' => self::$profile
            );
        }
        foreach (self::$profileHandlers as $handler) {
            call_user_func($handler, self::$profile);
        }
    }
}
