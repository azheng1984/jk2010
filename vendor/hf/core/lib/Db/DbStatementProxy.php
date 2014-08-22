<?php
namespace Hyperframework\Db;

use Hyperframework\Config;
use Traversable;

class DbStatementProxy { //implements Traversable {
    private $statement;
    private $connection;
    private $isProfilerEnabled;

    public function __construct($statement, $connection) {
        var_dump($statement->next());
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

    public function bindColumn (
        $column ,&$param , int $type = null, $maxlen = null, $driverdata = null
    ) {

    }

    public function bindParam (
        $parameter,
        &$variable,
        $data_type = PDO::PARAM_STR, $length = null, $driver_options = null
    ) {
    }

    public function bindValue ($parameter ,  $value ,  $data_type = PDO::PARAM_STR ) {

    }

    public function closeCursor () {

    }

    public function columnCount () {
        
    }

    public function debugDumpParams () {

    }

    public function errorCode () {
    }

    public function errorInfo () {

    }

    public function fetch ($fetch_style, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0) { 
    }
    public function fetchAll ($fetch_style, $fetch_argument = null, $ctor_args = array()) {

    }

    public function fetchColumn ($column_number = 0) {

    }

    public function fetchObject ($class_name = "stdClass", $ctor_args = null) {
        
    }

    public function getAttribute ($attribute ) {

    }

    public function getColumnMeta ($column) {
    }

    public function nextRowset () {

    }

    public function rowCount () {
    }

    public function setAttribute ($attribute, $value) {
    }

    public function setFetchMode ($mode) {
    }

    public function __call($method, $params) {
        call_user_func_array(array($this->statement, $method), $params);
    }
}
