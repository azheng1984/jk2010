<?php
namespace Hyperframework\Db;

use Hyperframework\Config;

class DbStatementProxy {
    private $statement;
    private $connection;
    private $isProfilerEnabled;

    public function __construct($statement, $connection) {
        $this->statement = $statement;
        $this->connection = $connection;
        $this->isProfilerEnabled =
            Config::get('hyperframework.db.profiler.enable') === true;
    }

    public function execute($params = null) {
        if ($this->isProfilerEnabled) {
            DbProfiler::onStatementExecuting($this);
        }
        $result = $this->statement->execute($params);
        if ($this->isProfilerEnabled !== null) {
            DbProfiler::onStatementExecuted($this);
        }
        return $result;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getSql() {
        return $this->statement->queryString;
    }

    public function __call($method, $params) {
        call_user_func_array(array($this->statement, $method), $params);
    }
}
