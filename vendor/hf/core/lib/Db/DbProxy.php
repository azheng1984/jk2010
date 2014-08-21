<?php
namespace Hyperframework\Db;

use PDO;

class DbProxy extends PDO {
    private $connectionName;
    private $filters = array();
    private $count = 0;

    public function setConnectionName($name) {
        $this->connectionName = $name;
    }

    public function addFilter($filter) {
        self::$filters[] = $filter;
    }

    public function prepare($sql, $driverOptions = array()) {
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
        ++$this->count;
        foreach ($this->preprocessers as $callback) {
            $sql = $callback($sql);
        }
        foreach ($preExecuteEventHandlers as $callback) {
            $callback($this->count);
        }
        $result = parent::exec($sql);
        foreach ($postExecuteEventHandlers as $callback) {
            $callback($this->count);
        }
        return $result;
    }

    public function addSqlPreprocessor($callback) {
    }

    public function addPreExecuteEventHandler($callback) {
    }

    public function addPostExecuteEventHandler($callback) {
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
