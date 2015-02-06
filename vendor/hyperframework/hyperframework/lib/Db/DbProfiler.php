<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Logging\Logger;

class DbProfiler {
    private static $profile;

    public static function isEnabled() {
        return Config::getBoolean('hyperframework.db.profiler.enable', false);
    }

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

    public static function onConnectionExecuted($connection, $result) {
        self::handleProfile();
    }

    public static function onStatementExecuting($statement) {
        self::initializeProfile(
            $statement->getConnection(), ['sql' => $statement->getsql()]
        );
    }

    public static function onStatementExecuted($statement) {
        self::handleProfile();
    }

    private static function initializeProfile($connection, array $profile) {
        self::$profile = [];
        $name = $connection->getName();
        if ($name !== 'default') {
            self::$profile['connection_name'] = $name;
        }
        self::$profile = self::$profile + $profile;
        self::$profile['start_time'] = microtime(true);
    }

    private static function handleProfile() {
        self::$profile['running_time'] =
            microtime(true) - self::$profile['start_time'];
        $isLoggerEnabled = Config::getBoolean(
            'hyperframework.db.profiler.logger.enable', true
        );
        if ($isLoggerEnabled) {
            $callback = function() {
                $log = 'Database';
                if (isset(self::$profile['connection_name'])) {
                    $log .= " '" . self::$profile['connection_name'] . "'";
                }
                $runningTime = sprintf('%.6F', self::$profile['running_time']);
                $log .= " operation ({$runningTime}): ";
                if (isset(self::$profile['sql'])) {
                    $log .= self::$profile['sql'];
                } else {
                    $log .= self::$profile['transaction'] . ' transaction';
                }
                return $log;
            };
            $loggerClass = self::getCustomLoggerClass();
            if ($loggerClass !== null) {
                $loggerClass::debug($callback);
            } else {
                Logger::debug($callback);
            }
        }
        $profileHandlerClass = Config::getString(
            'hyperframework.db.profiler.profile_handler_class', ''
        );
        if ($profileHandlerClass !== '') {
            if (class_exists($profileHandlerClass) === false) {
                throw new ClassNotFoundException(
                    "Database operation profile handler class"
                        . " '$profileHandlerClass' does not exist, defined in "
                        . "'hyperframework.db.profiler.profile_handler_class'."
                );
            }
            $profileHandler = new $profileHandlerClass;
            $profileHandler->handle($profile);
        }
    }

    private static function getCustomLoggerClass() {
        $loggerClass = Config::getString(
            'hyperframework.db.profiler.logger.class', ''
        );
        if ($loggerClass !== '') {
            if (class_exists($loggerClass) === false) {
                throw new ClassNotFoundException(
                    "Logger class '$class' does not exist, defined in "
                        . "'hyperframework.db.profiler.logger.class'."
                );
            }
            return $loggerClass;
        }
    }
}
