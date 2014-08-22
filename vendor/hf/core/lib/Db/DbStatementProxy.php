<?php
namespace Hyperframework\Db;

use PDO;
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

    public function bindColumn(
        $column,
        &$param,
        $type = PDO::PARAM_STR,
        $maxLength = null,
        $driverOptions = null
    ) {
        return $this->statement->bindColumn(
            $column, $param, $type, $maxLength, $driverOptions
        );
    }

    public function bindParam(
        $param,
        &$variable,
        $dataType = PDO::PARAM_STR,
        $length = null,
        $driverOptions = null
    ) {
        return $this->statement->bindParam(
            $param, $variable, $dataType, $length, $driverOptions
        );
    }

    public function bindValue($param, $value, $dataType = PDO::PARAM_STR) {
        return $this->statement->bindValue($param, $value, $dataType);
    }

    public function closeCursor() {
        return $this->statement->closeCursor();
    }

    public function columnCount() {
        return $this->statement->columnCount();
    }

    public function debugDumpParams() {
        $this->statement->debugDumpParams();
    }

    public function errorCode() {
        return $this->statement->errorCode();
    }

    public function errorInfo() {
        return $this->statement->errorInfo();
    }

    public function fetch(
        $fetchStyle = null,
        $cursorOrientation = PDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ) {
        return $this->statement->fetch(
            $fetchStyle, $cursorOrientation, $cursorOffset
        );
    }

    public function fetchAll(
        $fetchStyle = null,
        $fetchArgument = null,
        $constructorArguments = array()
    ) {
        if ($fetchStyle === null) {
            return $this->statement->fetchAll();
        }
        if ($fetchStyle === PDO::FETCH_COLUMN || $fetchStyle === PDO::FETCH_FUNC
        ) {
            return $this->statement->fetchAll($fetchStyle, $fetchArgument);
        }
        if ($fetchStyle === PDO::FETCH_CLASS) {
            return $this->statement->fetchAll(
                $fetchStyle, $fetchArgument, $constructorArguments
            );
        }
        return $this->statement->fetchAll($fetchStyle);
    }

    public function fetchColumn($columnNumber = 0) {
        return $this->statement->fetchColumn($columnNumber);
    }

    public function fetchObject(
        $className = "stdClass", $constructArguments = array()
    ) {
        return $this->statement->fetchObject($className, $constructArguments); 
    }

    public function getAttribute($attribute) {
        return $this->statement->getAttribute($attribute);
    }

    public function getColumnMeta($column) {
        return $this->statement->getColumnMeta($column);
    }

    public function nextRowset() {
        return $this->statement->nextRowset();
    }

    public function rowCount() {
        return $this->statement->rowCount();
    }

    public function setAttribute($attribute, $value) {
        return $this->statement->setAttribute($attribute, $value);
    }

    public function setFetchMode($mode) {
        return call_user_func_array(
            array($this->statement, 'setFetchMode'), func_get_args()
        );
    }

//    public function __call($n, $b) {
//       return call_user_func_array(array($this->statement,$n), $b); 
//    }
}
