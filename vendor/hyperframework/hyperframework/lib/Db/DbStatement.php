<?php
namespace Hyperframework\Db;

use PDO;

class DbStatement {
    private $pdoStatement;
    private $connection;

    public function __construct($pdoStatement, $connection) {
        $this->pdoStatement = $pdoStatement;
        $this->connection = $connection;
    }

    public function execute($params = null) {
        DbProfiler::onPreparedStatementExecuting($this);
        $result = $this->pdoStatement->execute($params);
        DbProfiler::onPreparedStatementExecuted();
        return $result;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getSql() {
        return $this->pdoStatement->queryString;
    }

    public function bindColumn(
        $column,
        &$param,
        $type = PDO::PARAM_STR,
        $maxLength = null,
        $driverOptions = null
    ) {
        return $this->pdoStatement->bindColumn(
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
        return $this->pdoStatement->bindParam(
            $param, $variable, $dataType, $length, $driverOptions
        );
    }

    public function bindValue($param, $value, $dataType = PDO::PARAM_STR) {
        return $this->pdoStatement->bindValue($param, $value, $dataType);
    }

    public function closeCursor() {
        return $this->pdoStatement->closeCursor();
    }

    public function columnCount() {
        return $this->pdoStatement->columnCount();
    }

    public function debugDumpParams() {
        $this->pdoStatement->debugDumpParams();
    }

    public function errorCode() {
        return $this->pdoStatement->errorCode();
    }

    public function errorInfo() {
        return $this->pdoStatement->errorInfo();
    }

    public function fetch(
        $fetchStyle = null,
        $cursorOrientation = PDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ) {
        return $this->pdoStatement->fetch(
            $fetchStyle, $cursorOrientation, $cursorOffset
        );
    }

    public function fetchAll(
        $fetchStyle = null,
        $fetchArgument = null,
        array $constructorArguments = []
    ) {
        switch (func_num_args()) {
            case 0: return $this->pdoStatement->fetchAll();
            case 1: return $this->pdoStatement->fetchAll($fetchStyle);
            case 2: return $this->pdoStatement->fetchAll(
                $fetchStyle, $fetchArgument
            );
            default: return $this->pdoStatement->fetchAll(
                $fetchStyle, $fetchArgument, $constructorArguments
            );
        }
    }

    public function fetchColumn($columnNumber = 0) {
        return $this->pdoStatement->fetchColumn($columnNumber);
    }

    public function fetchObject(
        $className = "stdClass", array $constructorArguments = []
    ) {
        return $this->pdoStatement->fetchObject(
            $className, $constructorArguments
        );
    }

    public function getAttribute($attribute) {
        return $this->pdoStatement->getAttribute($attribute);
    }

    public function getColumnMeta($column) {
        return $this->pdoStatement->getColumnMeta($column);
    }

    public function nextRowset() {
        return $this->pdoStatement->nextRowset();
    }

    public function rowCount() {
        return $this->pdoStatement->rowCount();
    }

    public function setAttribute($attribute, $value) {
        return $this->pdoStatement->setAttribute($attribute, $value);
    }

    public function setFetchMode($mode) {
        return call_user_func_array(
            [$this->pdoStatement, 'setFetchMode'], func_get_args()
        );
    }
}
