<?php
namespace Hyperframework\Db;

class DbProxyStatement {
    private $statement;
    private $sql;
    private $connectionName;

    public function __construct($statement, $sql, $connectionName) {
        $this->statement = $statement;
        $this->sql = $sql;
        $this->connectionName = $connectionName;
    }

    public function execute($params = null) {
        echo $this->sql;
        $this->statement->execute($params);
    }

    public function __call($method, $params) {
        call_user_func_array(array($this->statement, $method), $params);
    }
}
