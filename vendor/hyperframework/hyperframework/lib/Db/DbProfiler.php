<?php
namespace Hyperframework\Db;

use DateTime;
use DateTimeZone;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Logging\Logger;

class DbProfiler {
    private static $profile;
    private static $profileHandler;

    public static function isEnabled() {
        return Config::getBoolean('hyperframework.db.profiler.enable', false);
    }

    public static function onTransactionOperationExecuting(
        $connection, $operation
    ) {
        if (static::isEnabled()) {
            self::initializeProfile($connection, ['transaction' => $operation]);
        }
    }

    public static function onTransactionOperationExecuted() {
        if (static::isEnabled()) {
            self::handleProfile();
        }
    }

    public static function onSqlStatementExecuting($connection, $sql) {
        if (static::isEnabled()) {
            self::initializeProfile($connection, ['sql' => $sql]);
        }
    }

    public static function onSqlStatementExecuted() {
        if (static::isEnabled()) {
            self::handleProfile();
        }
    }

    public static function onPreparedStatementExecuting($statement) {
        if (static::isEnabled()) {
            self::initializeProfile(
                $statement->getConnection(), ['sql' => $statement->getsql()]
            );
        }
    }

    public static function onPreparedStatementExecuted() {
        if (static::isEnabled()) {
            self::handleProfile();
        }
    }

    public static function setProfileHandler($handler) {
        self::$profileHandler = $handler;
    }

    public static function getProfileHandler() {
        if (self::$profileHandler === null) {
            $configName = 'hyperframework.db.profiler.profile_handler_class';
            $profileHandlerClass = Config::getString($configName, '');
            if ($profileHandlerClass !== '') {
                if (class_exists($profileHandlerClass) === false) {
                    throw new ClassNotFoundException(
                        "Database operation profile handler class "
                            . "'$profileHandlerClass' does not exist,"
                            . " set using config '$configName'."
                    );
                }
                self::$profileHandler = new $profileHandlerClass;
            } else {
                self::$profileHandler = false;
            }
        }
        if (self::$profileHandler === false) {
            return;
        }
        return self::$profileHandler;
    }

    private static function initializeProfile($connection, array $profile) {
        self::$profile = [];
        $name = $connection->getName();
        if ($name !== 'default') {
            self::$profile['connection_name'] = $name;
        }
        self::$profile = self::$profile + $profile;
        self::$profile['start_time'] = self::getTime();
    }

    private static function getTime() {
        $segments = explode(' ', microtime());
        $segments[0] = (float)$segments[0];
        $segments[1] = (float)$segments[1];
        return $segments;
    }

    private static function handleProfile() {
        $endTime = self::getTime();
        self::$profile['running_time'] = (float)sprintf(
            '%.6F',
            $endTime[1] - self::$profile['start_time'][1] + $endTime[0]
                - self::$profile['start_time'][0]
        );
        self::$profile['start_time'] = DateTime::createFromFormat(
            'U.u', self::$profile['start_time'][1] . '.'
                . (int)(self::$profile['start_time'][0] * 1000000)
        )->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        $isLoggerEnabled = Config::getBoolean(
            'hyperframework.db.profiler.enable_logger', true
        );
        if ($isLoggerEnabled) {
            $callback = function() {
                $log = '[database operation] ';
                if (isset(self::$profile['connection_name'])) {
                    $log .= "connection: "
                        . self::$profile['connection_name'] . " | ";
                }
                $log .= "time: " .
                    sprintf('%.6F', self::$profile['running_time']) . " | ";
                if (isset(self::$profile['sql'])) {
                    $log .= 'sql: ' . self::$profile['sql'];
                } else {
                    $log .= 'transaction: ' . self::$profile['transaction'];
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
        $profileHandler = static::getProfileHandler();
        if ($profileHandler !== null) {
            $profileHandler->handle(self::$profile);
        }
    }

    private static function getCustomLoggerClass() {
        $class = Config::getString(
            'hyperframework.db.profiler.logger_class', ''
        );
        if ($class !== '') {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Logger class '$class' does not exist, set using config "
                        . "'hyperframework.db.profiler.logger_class'."
                );
            }
            return $class;
        }
    }
}
