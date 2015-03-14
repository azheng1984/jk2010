<?php
namespace Hyperframework\Db;

use DateTime;
use DateTimeZone;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Logging\Logger;

class DbProfilerEngine {
    private $profile;
    private $profileHandler;

    public function onTransactionOperationExecuting(
        $connection, $operation
    ) {
        $this->initializeProfile($connection, ['transaction' => $operation]);
    }

    public function onTransactionOperationExecuted() {
        $this->handleProfile();
    }

    public function onSqlStatementExecuting($connection, $sql) {
        $this->initializeProfile($connection, ['sql' => $sql]);
    }

    public function onSqlStatementExecuted() {
        $this->handleProfile();
    }

    public function onPreparedStatementExecuting($statement) {
        $this->initializeProfile(
            $statement->getConnection(), ['sql' => $statement->getsql()]
        );
    }

    public function onPreparedStatementExecuted() {
        $this->handleProfile();
    }

    public function setProfileHandler($handler) {
        $this->profileHandler = $handler;
    }

    public function getProfileHandler() {
        if ($this->profileHandler === null) {
            $configName = 'hyperframework.db.profiler.profile_handler_class';
            $profileHandlerClass = Config::getString($configName, '');
            if ($profileHandlerClass !== '') {
                if (class_exists($profileHandlerClass) === false) {
                    throw new ClassNotFoundException(
                        "Database operation profile handler class "
                            . "'$profileHandlerClass' does not exist,"
                            . " set using config '$configName'."
                    );
                }
                $this->profileHandler = new $profileHandlerClass;
            } else {
                $this->profileHandler = false;
            }
        }
        if ($this->profileHandler === false) {
            return;
        }
        return $this->profileHandler;
    }

    private function initializeProfile($connection, array $profile) {
        $this->profile = [];
        $name = $connection->getName();
        if ($name !== 'default') {
            $this->profile['connection_name'] = $name;
        }
        $this->profile = $this->profile + $profile;
        $this->profile['start_time'] = $this->getTime();
    }

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
        $isLoggerEnabled = Config::getBoolean(
            'hyperframework.db.profiler.enable_logger', true
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

    private function getCustomLoggerClass() {
        $class = Config::getString(
            'hyperframework.db.profiler.logger_class', ''
        );
        if ($class !== '') {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Logger class '$class' does not exist, set using config "
                        . "'hyperframework.db.profiler.logger_class'."
                );
            }
            return $class;
        }
    }
}
