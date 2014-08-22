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

    public function bindColumn(
        $column,
        &$param,
        $type = null,
        $maxLength = null,
        $driverData = null
    ) {
        $this->statement->bindColumn(
            $column, $param, $type, $maxLength, $driverData
        );
    }

    public function bindParam(
        $param,
        &$variable,
        $dataType = PDO::PARAM_STR,
        $length = null,
        $driverOptions = null
    ) {
        $this->statement->bindParam(
            $param, $variable, $dataType, $length, $driverOptions
        );
    }

    public function bindValue($param, $value, $dataType = PDO::PARAM_STR) {
        $this->statement->bindValue($param, $value, $dataType);
    }

    public function closeCursor() {
        $this->statement->closeCursor();
    }

    public function columnCount() {
        $this->statement->columnCount();
    }

    public function debugDumpParams() {
        $this->statement->debugDumpParams();
    }

    public function errorCode() {
        $this->statement->errorCode();
    }

    public function errorInfo() {
        $this->statement->errorInfo();
    }

    public function fetch(
        $fetchStyle,
        $cursorOrientation = PDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ) {
        $this->statement->fetch(
            $fetchStyle, $cursorOrientation, $cursorOffset
        );
    }

    public function fetchAll(
        $fetchStyle, $fetchArgument = null, $constructorArguments = array()
    ) {
        $this->statement->fetchAll(
            $fetchStyle, $fetchArgument, $constructorArguments
        );
    }

    public function fetchColumn($columnNumber = 0) {
        $this->statement->fetchColumn($columnNumber);
    }

    public function fetchObject(
        $className = "stdClass", $constructArguments = null
    ) {
        $this->statement->fetchObject($className, $constructArguments); 
    }

    public function getAttribute($attribute) {
        $this->statement->getAttribute($attribute);
    }

    public function getColumnMeta($column) {
        $this->statement->getColumnMeta($column);
    }

    public function nextRowset() {
        $this->statement->nextRowset();
    }

    public function rowCount() {
        $this->statement->rowCount();
    }

    public function setAttribute($attribute, $value) {
        $this->statement->setAttribute($attribute, $value);
    }

    public function setFetchMode($mode) {
        $this->statement->setFetchMode($mode);
    }
}
