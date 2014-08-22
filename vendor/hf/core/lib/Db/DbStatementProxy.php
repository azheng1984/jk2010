<?php
namespace Hyperframework\Db;

class DbStatementProxy {
    private $statement;
    private $connection;
    private static $executingEventHandlers = array();
    private static $executedEventHandlers = array();

    public function __construct($statement, $connection) {
        $this->statement = $statement;
        $this->connection = $connection;
    }

    public function execute($params = null) {
        self::triggerExecutingEvent();
        $result = $this->statement->execute($params);
        self::triggerExecutedEvent();
    }

    public function getConnection() {
        return $this->connection;
    }

    protected function triggerExecutingEvent() {
       foreach (self::$executingEventHandlers as $callback) {
           call_user_func($callback, $this);
       }
    }

    protected function triggerExecutedEvent() {
       foreach (self::$executedEventHandlers as $callback) {
           call_user_func($callback, $this);
       }
    }

    public static function addExecutingEventHandler($callback) {
        self::$executingEventHandlers[] = $callback;
    }

    public static function addExecutedEventHandler($callback) {
        self::$executedEventHandlers[] = $callback;
    }

    public function __call($method, $params) {
        call_user_func_array(array($this->statement, $method), $params);
    }
}
