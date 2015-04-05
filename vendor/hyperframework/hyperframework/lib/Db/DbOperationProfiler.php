<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class DbOperationProfiler {
    /**
     * @return bool
     */
    public static function isEnabled() {
        return Config::getBool(
            'hyperframework.db.operation_profiler.enable', false
        );
    }

    /**
     * @param DbConnection $connection
     * @param string $operation
     */
    public static function onTransactionOperationExecuting(
        DbConnection $connection, $operation
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

    /**
     * @param DbConnection $connection
     * @param string $sql
     */
    public static function onSqlStatementExecuting(
        DbConnection $connection, $sql
    ) {
        if (static::isEnabled()) {
            static::getEngine()->onSqlStatementExecuting($connection, $sql);
        }
    }

    public static function onSqlStatementExecuted() {
        if (static::isEnabled()) {
            static::getEngine()->onSqlStatementExecuted();
        }
    }

    /**
     * @param DbStatement $statement
     */
    public static function onPreparedStatementExecuting(
        DbStatement $statement
    ) {
        if (static::isEnabled()) {
            static::getEngine()->onPreparedStatementExecuting($statement);
        }
    }

    public static function onPreparedStatementExecuted() {
        if (static::isEnabled()) {
            static::getEngine()->onPreparedStatementExecuted();
        }
    }

    /**
     * @param DbOperationProfileHandlerInterface $handler
     */
    public static function setProfileHandler(
        DbOperationProfileHandlerInterface $handler = null
    ) {
        static::getEngine()->setProfileHandler($handler);
    }

    /**
     * @return DbOperationProfileHandlerInterface
     */
    public static function getProfileHandler() {
        return static::getEngine()->getProfileHandler();
    }

    /**
     * @return object
     */
    public static function getEngine() {
        $engine = Registry::get('hyperframework.db.operation_profiler_engine');
        if ($engine === null) {
            $configName = 'hyperframework.db.operation_profiler.engine_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $engine = new DbOperationProfilerEngine;
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

    /**
     * @param object $engine
     */
    public static function setEngine($engine) {
        Registry::set('hyperframework.db.operation_profiler_engine', $engine);
    }
}
