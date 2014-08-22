<?php
namespace Hyperframework\Db;

use PDO;

class DbConnection extends PDO {
    private $name;
    private $identifierQuotationMarks;
    private static $executingEventHandlers = array();
    private static $executedEventHandlers = array();

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

    public function exec($sql) {
        self::triggerExecutingEvent($sql);
        $result = parent::exec($sql);
        self::triggerExecutedEvent($result);
        return $result;
    }

    public function query($sql) {
        self::triggerExecutingEvent($sql, true);
        $result = parent::query($sql);
        self::triggerExecutedEvent($result);
        return $result;
    }

    protected function triggerExecutingEvent($sql, $isQuery) {
       foreach (self::$executingEventHandlers as $callback) {
           call_user_func($callback, $this, $sql, $isQuery);
       }
    }

    protected function triggerExecutedEvent($result) {
       foreach (self::$executedEventHandlers as $callback) {
           call_user_func($callback, $this, $result);
       }
    }

    public static function addExecutingEventHandler($callback) {
        self::$executingEventHandlers[] = $callback;
    }

    public static function addExecutedEventHandler($callback) {
        self::$executedEventHandlers[] = $callback;
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
