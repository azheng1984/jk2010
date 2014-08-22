<?php
namespace Hyperframework\Db;

class DbProfiler {
    public static function onConnectionExecuting($connection, $sql, $isQuery) {
    }

    public static function onConnectionExecuted($connection, $result) {
    }

    public static function onStatementExecuting($statement) {
    }

    public static function onStatementExecuted($statement) {
    }
}
