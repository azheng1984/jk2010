<?php
namespace Hyperframework\Db;

use PDO;
use Exception;
use Hyperframework\Common\Config;

class DbConnection extends PDO {
    private $name;
    private $isProfilerEnabled;
    private $identifierQuotationMarks;

    public function __construct(
        $name, $dsn, $userName = null, $password = null, $driverOptions = null
    ) {
        $this->name = $name;
        $this->isProfilerEnabled =
            Config::getBoolean('hyperframework.db.profiler.enable');
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
        return $this->sendSql($sql);
    }

    public function query(
        $sql, $fetchStyle = null, $extraParam1 = null, $extraParam2 = null
    ) {
        $argumentCount = func_num_args();
        if ($argumentCount === 1) {
            return $this->sendSql($sql, true);
        }
        if ($argumentCount === 2) {
            return $this->sendSql($sql, true, array($fetchStyle));
        }
        if ($argumentCount === 3) {
            return $this->sendSql($sql, true, array($fetchStyle, $extraParam1));
        }
        return $this->sendSql(
            $sql, true, array($fetchStyle, $extraParam1, $extraParam2)
        );
    }

    public function beginTransaction() {
        if ($this->isProfilerEnabled) {
            DbProfiler::onTransactionOperationExecuting($this, 'begin');
        }
        parent::beginTransaction();
        if ($this->isProfilerEnabled) {
            DbProfiler::onTransactionOperationExecuted($this, 'begin');
        }
    }

    public function commit() {
        if ($this->isProfilerEnabled) {
            DbProfiler::onTransactionOperationExecuting($this, 'commit');
        }
        parent::commit();
        if ($this->isProfilerEnabled) {
            DbProfiler::onTransactionOperationExecuted($this, 'commit');
        }
    }

    public function rollBack() {
        if ($this->isProfilerEnabled) {
            DbProfiler::onTransactionOperationExecuting($this, 'rollback');
        }
        parent::rollBack();
        if ($this->isProfilerEnabled) {
            DbProfiler::onTransactionOperationExecuted($this, 'rollback');
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
                    case 3:
                        $result = parent::query(
                            $sql,
                            $fetchOptions[0],
                            $fetchOptions[1],
                            $fetchOptions[2]
                        );
                       break;
                    default:
                       throw new Exception;
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
