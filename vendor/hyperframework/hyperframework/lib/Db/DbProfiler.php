<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class DbProfiler {
    public static function isEnabled() {
        return Config::getBool('hyperframework.db.profiler.enable', false);
    }

    public static function onTransactionOperationExecuting(
        $connection, $operation
    ) {
        if (static::isEnabled()) {
            static::getengine()->ontransactionoperationexecuting(
                $connection, $operation
            );
        }
    }

    public static function onTransactionOperationExecuted() {
        if (static::isEnabled()) {
            static::getEngine()->onTransactionOperationExecuted();
        }
    }

    public static function onSqlStatementExecuting($connection, $sql) {
        if (static::isEnabled()) {
            static::getEngine()->onSqlStatementExecuting($connection, $sql);
        }
    }

    public static function onSqlStatementExecuted() {
        if (static::isEnabled()) {
            static::getEngine()->onSqlStatementExecuted();
        }
    }

    public static function onPreparedStatementExecuting($statement) {
        if (static::isEnabled()) {
            static::getEngine()->onPreparedStatementExecuting($statement);
        }
    }

    public static function onPreparedStatementExecuted() {
        if (static::isEnabled()) {
            static::getEngine()->onPreparedStatementExecuted();
        }
    }

    public static function setProfileHandler($handler) {
        static::getEngine()->setProfileHandler($handler);
    }

    public static function getProfileHandler() {
        return static::getEngine()->getProfileHandler();
    }

    public static function getEngine() {
        $engine = Registry::get('hyperframework.db.profiler_engine');
        if ($engine === null) {
            $configName = 'hyperframework.db.profiler.engine_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $engine = new DbProfilerEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not"
                            . " exist, set using config '$configName'."
                    );
                }
                $engine = new $class;
            }
            static::setEngine($engine);
        }
        return $engine;
    }

    public static function setEngine($engine) {
        Registry::set('hyperframework.db.profiler_engine', $engine);
    }
}
