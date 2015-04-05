<?php
namespace Hyperframework\Db;

use PDO;

class DbConnection extends PDO {
    private $name;
    private $identifierQuotationMarks;

    /**
     * @param string $name
     * @param string $dsn
     * @param string $userName
     * @param string $password
     * @param array $driverOptions
     */
    public function __construct(
        $name,
        $dsn,
        $userName = null,
        $password = null,
        array $driverOptions = null
    ) {
        $this->name = $name;
        parent::__construct($dsn, $userName, $password, $driverOptions);
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $sql
     * @param array $driverOptions
     * @return DbStatement
     */
    public function prepare($sql, $driverOptions = []) {
        $pdoStatement = parent::prepare($sql, $driverOptions);
        return new DbStatement($pdoStatement, $this);
    }

    /**
     * @param string $sql
     * @return int
     */
    public function exec($sql) {
        return $this->sendSql($sql);
    }

    /**
     * @param string $sql
     * @param int $fetchStyle
     * @param int $extraParam1
     * @param mixed $extraParam2
     * @return DbStatement
     */
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
        DbOperationProfiler::onTransactionOperationExecuting($this, 'begin');
        parent::beginTransaction();
        DbOperationProfiler::onTransactionOperationExecuted();
    }

    public function commit() {
        DbOperationProfiler::onTransactionOperationExecuting($this, 'commit');
        parent::commit();
        DbOperationProfiler::onTransactionOperationExecuted();
    }

    public function rollBack() {
        DbOperationProfiler::onTransactionOperationExecuting($this, 'rollback');
        parent::rollBack();
        DbOperationProfiler::onTransactionOperationExecuted();
    }

    /**
     * @param string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier) {
        if ($this->identifierQuotationMarks === null) {
            $this->identifierQuotationMarks =
                $this->getIdentifierQuotationMarks();
        }
        return $this->identifierQuotationMarks[0] . $identifier
            . $this->identifierQuotationMarks[1];
    }

    /**
     * @return string[]
     */
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

    /**
     * @param string $sql
     * @param bool $isQuery
     * @param string $fetchOptions
     * @return mixed
     */
    private function sendSql(
        $sql, $isQuery = false, array $fetchOptions = null
    ) {
        DbOperationProfiler::onSqlStatementExecuting($this, $sql);
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
        DbOperationProfiler::onSqlStatementExecuted();
        return $result;
    }
}
