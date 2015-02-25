<?php
namespace Hyperframework\Db;

use PDO;
use Hyperframework\Common\Config;

class DbConnection extends PDO {
    private $name;
    private $identifierQuotationMarks;

    public function __construct(
        $name, $dsn, $userName = null, $password = null,
        array $driverOptions = null
    ) {
        $this->name = $name;
        parent::__construct($dsn, $userName, $password, $driverOptions);
    }

    public function getName() {
        return $this->name;
    }

    public function prepare($sql, array $driverOptions = []) {
        $pdoStatement = parent::prepare($sql, $driverOptions);
        return new DbStatement($pdoStatement, $this);
    }

    public function exec($sql) {
        return $this->sendSql($sql);
    }

    public function query(
        $sql, $fetchStyle = null, $extraParam1 = null, $extraParam2 = null
    ) {
        switch (func_num_args()) {
            case 1: return $this->sendSql($sql, true);
            case 2: return $this->sendSql($sql, true, [$fetchStyle]);
            case 3: return $this->sendSql(
                $sql, true, [$fetchStyle, $extraParam1]
            );
            default: return $this->sendSql(
                $sql, true, [$fetchStyle, $extraParam1, $extraParam2]
            );
        }
    }

    public function beginTransaction() {
        DbProfiler::onTransactionOperationExecuting($this, 'begin');
        parent::beginTransaction();
        DbProfiler::onTransactionOperationExecuted($this, 'begin');
    }

    public function commit() {
        DbProfiler::onTransactionOperationExecuting($this, 'commit');
        parent::commit();
        DbProfiler::onTransactionOperationExecuted($this, 'commit');
    }

    public function rollBack() {
        DbProfiler::onTransactionOperationExecuting($this, 'rollback');
        parent::rollBack();
        DbProfiler::onTransactionOperationExecuted($this, 'rollback');
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
                return ['`', '`'];
            case 'sqlsrv':
                return ['[', ']'];
            default:
                return ['"', '"'];
        }
    }

    private function sendSql(
        $sql, $isQuery = false, array $fetchOptions = null
    ) {
        DbProfiler::onConnectionExecuting($this, $sql, $isQuery);
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
            $result = new DbStatement($result, $this);
        } else {
            $result = parent::exec($sql);
        }
        DbProfiler::onConnectionExecuted($this, $result);
        return $result;
    }
}
