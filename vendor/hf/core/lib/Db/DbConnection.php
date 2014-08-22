<?php
namespace Hyperframework\Db;

use PDO;

class DbConnection extends PDO {
    private static $profiler;
    private $name;
    private $identifierQuotationMarks;

    public function __construct(
        $name, $dsn, $userName = null, $password = null, $driverOptions = null
    ) {
        $this->name = $name;
        parent::__construct($dsn, $userName, $password, $driverOptions);
    }

    public function getName() {
        return $this->name;
    }

    public function prepare($sql, $driverOptions = array()) {
        $statement = parent::prepare($sql, $driverOptions);
        return new DbStatementProxy($statement, $this);
    }

    public static function setProfiler($profiler) {
        self::$profiler = $profiler;
    }

    public function exec($sql) {
        return self::sendSql($sql);
    }

    public function query($sql) {
        return self::sendSql($sql, true);
    }

    protected function sendSql($sql, $isQuery = false) {
        $profiler = self::$profiler;
        if ($profiler !== null) {
            $profiler::onConnectionExecuting($this, $sql, $isQuery);
        }
        $result = null;
        if ($isQuery) {
            $result = parent::query($sql);
        } else {
            $result = parent::exec($sql);
        }
        if ($profiler !== null) {
            $profiler::onConnectionExecuted($this, $result);
        }
        return $result;
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
