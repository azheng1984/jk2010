<?php
namespace Hyperframework\Db;

use DateTime;
use DateTimeZone;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Logging\Logger;

class DbOperationProfilerEngine {
    private $profile;
    private $profileHandler;

    /**
     * @param DbConnection $connection
     * @param string $operation
     */
    public function onTransactionOperationExecuting(
        DbConnection $connection, $operation
    ) {
        $this->initializeProfile($connection, ['transaction' => $operation]);
    }

    public function onTransactionOperationExecuted() {
        $this->handleProfile();
    }

    /**
     * @param DbConnection $connection
     * @param string $sql
     */
    public function onSqlStatementExecuting(DbConnection $connection, $sql) {
        $this->initializeProfile($connection, ['sql' => $sql]);
    }

    public function onSqlStatementExecuted() {
        $this->handleProfile();
    }

    /**
     * @param DbStatement $statement
     */
    public function onPreparedStatementExecuting(DbStatement $statement) {
        $this->initializeProfile(
            $statement->getConnection(), ['sql' => $statement->getsql()]
        );
    }

    public function onPreparedStatementExecuted() {
        $this->handleProfile();
    }

    /**
     * @param DbOperationProfileHandlerInterface $handler
     */
    public function setProfileHandler(
        DbOperationProfileHandlerInterface $handler = null
    ) {
        $this->profileHandler = $handler;
    }

    /**
     * @return DbOperationProfileHandlerInterface
     */
    public function getProfileHandler() {
        if ($this->profileHandler === null) {
            $configName =
                'hyperframework.db.operation_profiler.profile_handler_class';
            $profileHandlerClass = Config::getString($configName, '');
            if ($profileHandlerClass !== '') {
                if (class_exists($profileHandlerClass) === false) {
                    throw new ClassNotFoundException(
                        "Class '$profileHandlerClass' does not exist,"
                            . " set using config '$configName'."
                    );
                }
                $this->profileHandler = new $profileHandlerClass;
            }
        }
        return $this->profileHandler;
    }

    /**
     * @param DbConnection $connection
     * @param array $profile
     */
    private function initializeProfile(
        DbConnection $connection, array $profile
    ) {
        $this->profile = [];
        $name = $connection->getName();
        if ($name !== 'default') {
            $this->profile['connection_name'] = $name;
        }
        $this->profile = $this->profile + $profile;
        $this->profile['start_time'] = $this->getTime();
    }

    /**
     * @return float[]
     */
    private function getTime() {
        $segments = explode(' ', microtime());
        $segments[0] = (float)$segments[0];
        $segments[1] = (float)$segments[1];
        return $segments;
    }

    private function handleProfile() {
        $profile = $this->profile;
        $this->profile = null;
        $endTime = $this->getTime();
        $profile['running_time'] = (float)sprintf(
            '%.6F',
            $endTime[1] - $profile['start_time'][1] + $endTime[0]
                - $profile['start_time'][0]
        );
        $profile['start_time'] = DateTime::createFromFormat(
            'U.u', $profile['start_time'][1] . '.'
                . (int)($profile['start_time'][0] * 1000000)
        )->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        $isLoggerEnabled = Config::getBool(
            'hyperframework.db.operation_profiler.enable_logger', true
        );
        if ($isLoggerEnabled) {
            $callback = function() use ($profile) {
                $log = '[database operation] ';
                if (isset($profile['connection_name'])) {
                    $log .= "connection: "
                        . $profile['connection_name'] . " | ";
                }
                $log .= "time: " .
                    sprintf('%.6F', $profile['running_time']) . " | ";
                if (isset($profile['sql'])) {
                    $log .= 'sql: ' . $profile['sql'];
                } else {
                    $log .= 'transaction: ' . $profile['transaction'];
                }
                return $log;
            };
            $loggerClass = $this->getCustomLoggerClass();
            if ($loggerClass !== null) {
                $loggerClass::debug($callback);
            } else {
                Logger::debug($callback);
            }
        }
        $profileHandler = $this->getProfileHandler();
        if ($profileHandler !== null) {
            $profileHandler->handle($profile);
        }
    }

    /**
     * @return string
     */
    private function getCustomLoggerClass() {
        $configName = 'hyperframework.db.operation_profiler.logger_class';
        $class = Config::getString($configName, '');
        if ($class !== '') {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Class '$class' does not exist, set using config "
                        . "'$configName'."
                );
            }
            return $class;
        }
    }
}
