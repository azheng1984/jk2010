<?php
namespace Hyperframework\Db;

use PDO;

class DbConnection extends PDO {
    private $name;
    private $identifierQuotationMarks;
    private $filters = array();
    private static $index = 0;

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function prepare($sql, $driverOptions = array()) {
       // $index = ++self::$index;
       // foreach ($filters as $filter) {
       //     $sql = $filter->filterSql($sql);
       // }
        $statement = parent::prepare($sql, $driverOptions);
        return $statement;
       // foreach ($filters as $filter) {
       //     $filter->afterPrepare();
       // }
       // return new DbStatementProxy(
       //     $statement, $this, $sql, $this->connectionName
       // );
    }

    public function exec($sql) {
       // $index = ++self::$index;
       // foreach (self::$sqlFilters as $callback) {
       //     $sql = $callback($this, $sql);
       // }
       // foreach ($preExecuteEventHandlers as $callback) {
       //     $callback($count);
       // }
       // foreach ($postExecuteEventHandlers as $callback) {
       //     $callback($this->count);
       // }

        $result = parent::exec($sql);
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

    public function quoteIdentifier($identifier) {
        if ($this->identifierQuotationMarks === null) {
            $this->identifierQuotationMarks =
                $this->getIdentifierQuotationMarks();
        }
        return $this->identifierQuotationMarks[0] . $identifier
            . $this->identifierQuotationMarks[1];
    }

    protected function getIdentifierQuotationMarks() {
        switch ($this->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                return array('`', '`');
            case 'sqlsrv':
                return array('[', ']');
            default:
                return array('"', '"');
        }
    }
}
