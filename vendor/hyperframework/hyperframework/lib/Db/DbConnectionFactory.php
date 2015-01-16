<?php
namespace Hyperframework\Db;

use PDO;
use Hyperframework\Common\ConfigFileLoader;
use Hyperframework\Common\ConfigException;

class DbConnectionFactory {
    private $config;

    public function create($name = 'default') {
        $config = $this->getConfig($name);
        if (isset($config['dsn']) === false) {
            throw new ConfigException(
                "Field 'dsn' does not exist"
                    . " in connection config '$name'."
            );
        }
        $username = isset($config['username']) ? $config['username'] : null;
        $password = isset($config['password']) ? $config['password'] : null;
        $options = isset($config['options']) ? $config['options'] : null;

        $connection = new DbConnection(
            $name, $config['dsn'], $username, $password, $options
        );
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    private function getConfig($name) {
        if ($this->config === null) {
            $this->initializeConfig();
        }
        if ($name === 'default' && isset($this->config['dsn'])
            && is_string($this->config['dsn'])
        ) {
            return $this->config;
        }
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        throw new ConfigException(
            "Database connection config '$name' does not exist."
        );
    }

    private function initializeConfig() {
        $this->config = ConfigFileLoader::loadPhp(
            'db.php', 'hyperframework.db.config_path'
        );
    }
}
