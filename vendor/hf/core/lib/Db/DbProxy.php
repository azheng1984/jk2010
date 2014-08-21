<?php
namespace Hyperframework\Db;

use PDO;

class DbProxy extends PDO {
    private $connectionName;
    private $filters = array();
    private static $index = 0;

    public function setConnectionName($name) {
        $this->connectionName = $name;
    }

    public function getConnectionName() {
        return $this->connectionName;
    }

    public function prepare($sql, $driverOptions = array()) {
        $index = ++self::$index;
        foreach ($filters as $filter) {
            $sql = $filter->filterSql($sql);
        }
        $statement = parent::prepare($sql, $driverOptions);
        foreach ($filters as $filter) {
            $filter->afterPrepare();
        }
        return new DbProxyStatement(
            $statement, $this, $sql, $this->connectionName
        );
    }

    public function exec($sql) {
        $index = ++self::$index;
        foreach (self::$sqlFilters as $callback) {
            $sql = $callback($this, $sql);
        }
        foreach ($preExecuteEventHandlers as $callback) {
            $callback($count);
        }
        $result = parent::exec($sql);
        foreach ($postExecuteEventHandlers as $callback) {
            $callback($this->count);
        }
        return $result;
    }

    public static function addPrepareSqlEventHandler(
        $callback, $connectionName = null
    ) {
    }

    public static function addPreExecuteEventHandler($callback) {
    }

    public static function addPostExecuteEventHandler($callback) {
    }

    public function query($sql) {
        foreach ($preExecuteEventHandlers as $callback) {
            $callback();
        }
        return parent::query($sql);
        foreach ($postExecuteEventHandlers as $callback) {
            $callback();
        }
    }
}
