<?php
namespace Hyperframework\Db;

use PDO;
use Hyperframework\Common\Config;

class DbConnection extends PDO {
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

    public function prepare($sql, $driverOptions = []) {
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
            return $this->sendSql($sql, true, [$fetchStyle]);
        }
        if ($argumentCount === 3) {
            return $this->sendSql($sql, true, [$fetchStyle, $extraParam1]);
        }
        return $this->sendSql(
            $sql, true, [$fetchStyle, $extraParam1, $extraParam2]
        );
    }

    public function beginTransaction() {
        if (DbProfiler::isEnabled()) {
            DbProfiler::onTransactionOperationExecuting($this, 'begin');
        }
        parent::beginTransaction();
        if (DbProfiler::isEnabled()) {
            DbProfiler::onTransactionOperationExecuted($this, 'begin');
        }
    }

    public function commit() {
        if (DbProfiler::isEnabled()) {
            DbProfiler::onTransactionOperationExecuting($this, 'commit');
        }
        parent::commit();
        if (DbProfiler::isEnabled()) {
            DbProfiler::onTransactionOperationExecuted($this, 'commit');
        }
    }

    public function rollBack() {
        if (DbProfiler::isEnabled()) {
            DbProfiler::onTransactionOperationExecuting($this, 'rollback');
        }
        parent::rollBack();
        if (DbProfiler::isEnabled()) {
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

    private function sendSql(
        $sql, $isQuery = false, array $fetchOptions = null
    ) {
        if (DbProfiler::isEnabled()) {
            DbProfiler::onConnectionExecuting($this, $sql, $isQuery);
        }
        $result = null;
        if ($isQuery) {
            if ($fetchOptions === null) {
                $result = parent::query($sql);
            } else {
                switch (count($fetchOptions)) {
                    case 0:
                        break;
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
            $result = new DbStatementProxy($result, $this);
        } else {
            $result = parent::exec($sql);
        }
        if (DbProfiler::isEnabled()) {
            DbProfiler::onConnectionExecuted($this, $result);
        }
        return $result;
    }

    protected function getIdentifierQuotationMarks() {
        switch ($this->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                return ['`', '`'];
            case 'sqlsrv':
                return ['[', ']'];
            default:
                return ['"', '"'];
        }
    }
}
