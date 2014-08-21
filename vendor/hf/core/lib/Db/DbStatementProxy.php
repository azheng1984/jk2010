<?php
namespace Hyperframework\Db;

class DbStatementProxy {
    private $statement;
    private $sql;
    private $connection;

    public function __construct($statement, $connection, $sql) {
        $this->statement = $statement;
        $this->connection = $connection;
        $this->sql = $sql;
    }

    public function execute($params = null) {
        echo $this->sql;
        $this->statement->execute($params);
    }

    public function __call($method, $params) {
        call_user_func_array(array($this->statement, $method), $params);
    }
}
