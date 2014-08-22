<?php
namespace Hyperframework\Db;

class DbStatementProxy {
    private static $profiler;
    private $statement;
    private $connection;

    public function __construct($statement, $connection) {
        $this->statement = $statement;
        $this->connection = $connection;
    }

    public function execute($params = null) {
        $profiler = self::$profiler;
        if ($profiler !== null) {
            $profiler::onStatementExecuting($this);
        }
        $result = $this->statement->execute($params);
        if ($profiler !== null) {
            $profiler::onStatementExecuted($this);
        }
        return $result;
    }

    public function getConnection() {
        return $this->connection;
    }

    public static function setProfiler($profiler) {
        self::$profiler = $profiler;
    }

    public function getSql() {
        return $this->statement->queryString;
    }

    public function __call($method, $params) {
        call_user_func_array(array($this->statement, $method), $params);
    }
}
