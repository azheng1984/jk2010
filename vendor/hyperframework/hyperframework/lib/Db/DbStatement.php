<?php
namespace Hyperframework\Db;

use PDO;
use PDOStatement;

class DbStatement {
    private $pdoStatement;
    private $connection;

    /**
     * @param PDO $pdoStatement
     * @param DbConnection $connection
     */
    public function __construct(
        PDOStatement $pdoStatement, DbConnection $connection
    ) {
        $this->pdoStatement = $pdoStatement;
        $this->connection = $connection;
    }

    /**
     * @param array $params
     */
    public function execute(array $params = null) {
        DbProfiler::onPreparedStatementExecuting($this);
        $this->pdoStatement->execute($params);
        DbProfiler::onPreparedStatementExecuted();
    }

    /**
     * @return DbConnection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getSql() {
        return $this->pdoStatement->queryString;
    }

    /**
     * @param mixed $column
     * @param mixed &$param
     * @param int $type
     * @param int $maxLength
     * @param array $driverOptions
     */
    public function bindColumn(
        $column,
        &$param,
        $type = PDO::PARAM_STR,
        $maxLength = null,
        array $driverOptions = null
    ) {
        return $this->pdoStatement->bindColumn(
            $column, $param, $type, $maxLength, $driverOptions
        );
    }

    /**
     * @param mixed $param
     * @param mixed &$variable
     * @param int $dataType
     * @param int $length
     * @param array $driverOptions
     */
    public function bindParam(
        $param,
        &$variable,
        $dataType = PDO::PARAM_STR,
        $length = null,
        array $driverOptions = null
    ) {
        return $this->pdoStatement->bindParam(
            $param, $variable, $dataType, $length, $driverOptions
        );
    }

    /**
     * @param mixed $param
     * @param mixed $value
     * @param int $dataType
     */
    public function bindValue($param, $value, $dataType = PDO::PARAM_STR) {
        return $this->pdoStatement->bindValue($param, $value, $dataType);
    }

    public function closeCursor() {
        $this->pdoStatement->closeCursor();
    }

    /**
     * @return int
     */
    public function columnCount() {
        return $this->pdoStatement->columnCount();
    }

    public function debugDumpParams() {
        $this->pdoStatement->debugDumpParams();
    }

    /**
     * @return string
     */
    public function errorCode() {
        return $this->pdoStatement->errorCode();
    }

    /**
     * @return array
     */
    public function errorInfo() {
        return $this->pdoStatement->errorInfo();
    }

    /**
     * @param int $fetchStyle
     * @param int $cursorOrientation
     * @param int $cursorOffset
     */
    public function fetch(
        $fetchStyle = null,
        $cursorOrientation = PDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ) {
        return $this->pdoStatement->fetch(
            $fetchStyle, $cursorOrientation, $cursorOffset
        );
    }

    /**
     * @param int $fetchStyle
     * @param int $fetchArgument
     * @param array $constructorArguments
     */
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

    /**
     * @param int $columnNumber
     */
    public function fetchColumn($columnNumber = 0) {
        return $this->pdoStatement->fetchColumn($columnNumber);
    }

    /**
     * @param string $className
     * @param array $constructorArguments
     */
    public function fetchObject(
        $className = "stdClass", array $constructorArguments = []
    ) {
        return $this->pdoStatement->fetchObject(
            $className, $constructorArguments
        );
    }

    /**
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute($attribute) {
        return $this->pdoStatement->getAttribute($attribute);
    }

    /**
     * @param int $column
     * @return array
     */
    public function getColumnMeta($column) {
        return $this->pdoStatement->getColumnMeta($column);
    }

    /**
     * @return bool
     */
    public function nextRowset() {
        return $this->pdoStatement->nextRowset();
    }

    /**
     * @return bool
     */
    public function rowCount() {
        return $this->pdoStatement->rowCount();
    }

    /**
     * @param int $attribute
     * @param mixed $value
     */
    public function setAttribute($attribute, $value) {
        return $this->pdoStatement->setAttribute($attribute, $value);
    }

    /**
     * @param int $mode
     * @param mixed $extraParam1
     * @param array $extraParam2
     * @return mixed
     */
    public function setFetchMode(
        $mode, $extraParam1 = null, array $extraParam2 = null
    ) {
        return call_user_func_array(
            [$this->pdoStatement, 'setFetchMode'], func_get_args()
        );
    }
}
