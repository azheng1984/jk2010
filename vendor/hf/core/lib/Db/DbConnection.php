<?php
namespace Hyperframework\Db;

use PDO;
use Hyperframework\Config;

class DbConnection extends PDO {
    private $name;
    private $isProfilerEnabled;
    private $identifierQuotationMarks;

    public function __construct(
        $name, $dsn, $userName = null, $password = null, $driverOptions = null
    ) {
        $this->name = $name;
        $this->isProfilerEnabled =
            Config::get('hyperframework.db.profiler.enable') === true;
        parent::__construct($dsn, $userName, $password, $driverOptions);
    }

    public function getName() {
        return $this->name;
    }

    public function prepare($sql, $driverOptions = array()) {
        $statement = parent::prepare($sql, $driverOptions);
        return new DbStatementProxy($statement, $this);
    }

    public function exec($sql) {
        return self::sendSql($sql);
    }

    public function query(
        $sql, $fetchStyle = null, $extraParam1 = null, $extraParam2 = null
    ) {
        $argumentCount = func_num_args();
        if ($argumentCount === 1) {
            return self::sendSql($sql, true);
        }
        switch ($argumentCount) {
            case 2: return self::sendSql($sql, true, array($fetchStyle));
            case 3: return self::sendSql(
                $sql, true, array($fetchStyle, $extraParam1)
            );
            default: return self::sendSql(
                $sql, true, array($fetchStyle, $extraParam1, $extraParam2)
            );
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

    protected function sendSql($sql, $isQuery = false, $fetchOptions = null) {
        if ($this->isProfilerEnabled) {
            DbProfiler::onConnectionExecuting($this, $sql, $isQuery);
        }
        $result = null;
        if ($isQuery) {
            if ($fetchOptions === null) {
                $result = parent::query($sql);
            } else {
                switch (count($fetchOptions)) {
                    case 1:
                        $result = parent::query($sql, $fetchOptions[0]);
                        break;
                    case 2:
                        $result = parent::query(
                            $sql, $fetchOptions[0], $fetchOptions[1]
                        );
                        break;
                    default:
                        $result = parent::query(
                            $sql,
                            $fetchOptions[0],
                            $fetchOptions[1],
                            $fetchOptions[2]
                        );
                }
            }
        } else {
            $result = parent::exec($sql);
        }
        if ($this->isProfilerEnabled) {
            DbProfiler::onConnectionExecuted($this, $result);
        }
        return $result;
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
